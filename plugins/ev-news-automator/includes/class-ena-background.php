<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class ENA_Background {

    const JOB_TTL = 1800; // seconds — after this, a running job is considered stale

    public static function dispatch( string $job_type ): array {
        $existing = self::get_job();
        if ( $existing && $existing['status'] === 'running' ) {
            $age = time() - ( $existing['started_at'] ?? 0 );
            if ( $age < self::JOB_TTL ) {
                return [
                    'dispatched' => false,
                    'reason'     => 'already_running',
                    'job'        => self::public_job( $existing ),
                ];
            }
        }

        $job_id = wp_generate_uuid4();
        $token  = wp_generate_password( 40, false );

        $job = [
            'job_id'      => $job_id,
            'type'        => $job_type,
            'status'      => 'running',
            'started_at'  => time(),
            'token'       => $token,
            'result'      => null,
            'error'       => null,
            'finished_at' => null,
        ];

        update_option( ENA_OPT_ACTIVE_JOB, $job, false );

        wp_remote_post( admin_url( 'admin-ajax.php' ), [
            'timeout'   => 0.01,
            'blocking'  => false,
            'body'      => [
                'action'   => 'ena_bg_worker',
                'job_id'   => $job_id,
                'job_type' => $job_type,
                'token'    => $token,
            ],
            'cookies'   => [],
            'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
        ] );

        return [ 'dispatched' => true, 'job_id' => $job_id ];
    }

    public static function get_job(): ?array {
        $job = get_option( ENA_OPT_ACTIVE_JOB, null );
        return is_array( $job ) ? $job : null;
    }

    public static function validate_token( string $job_id, string $token ): bool {
        $job = self::get_job();
        return $job
            && $job['job_id'] === $job_id
            && $job['token']  === $token
            && $job['status'] === 'running';
    }

    public static function finish( string $job_id, array $result ): void {
        $job = self::get_job();
        if ( ! $job || $job['job_id'] !== $job_id ) return;

        $job['status']      = 'done';
        $job['result']      = $result;
        $job['finished_at'] = time();
        $job['token']       = '';
        update_option( ENA_OPT_ACTIVE_JOB, $job, false );
    }

    public static function fail( string $job_id, string $error ): void {
        $job = self::get_job();
        if ( ! $job || $job['job_id'] !== $job_id ) return;

        $job['status']      = 'error';
        $job['error']       = $error;
        $job['finished_at'] = time();
        $job['token']       = '';
        update_option( ENA_OPT_ACTIVE_JOB, $job, false );
    }

    public static function status_for_client(): array {
        $job = self::get_job();
        if ( ! $job ) return [ 'status' => 'idle' ];

        $pub = self::public_job( $job );

        // Treat stale running jobs as timed out
        if ( $pub['status'] === 'running' ) {
            $age = time() - ( $job['started_at'] ?? 0 );
            if ( $age > self::JOB_TTL ) {
                $pub['status'] = 'error';
                $pub['error']  = 'Job timed out after ' . self::JOB_TTL . 's without completing.';
            }
        }

        // Hide done/error bars after they've been visible long enough
        if ( $pub['status'] === 'done' ) {
            $age = time() - ( $job['finished_at'] ?? 0 );
            if ( $age > 300 ) return [ 'status' => 'idle' ]; // 5 min
        }
        if ( $pub['status'] === 'error' ) {
            $age = time() - ( $job['finished_at'] ?? 0 );
            if ( $age > 1800 ) return [ 'status' => 'idle' ]; // 30 min
        }

        return $pub;
    }

    private static function public_job( array $job ): array {
        unset( $job['token'] );
        return $job;
    }
}
