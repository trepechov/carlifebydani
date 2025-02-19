# CarLife by Dani WordPress Theme

This is a custom WordPress theme developed by Dani. It's designed for car enthusiasts and uses Tailwind CSS for styling.

## Features

-   Responsive design
-   Custom post types
-   Tailwind CSS for rapid UI development

## Installation

1. To run a local instance of WordPress. Follow the steps below:

-   Download and install [Local WP](https://localwp.com/). Ask someone for backup file.
-   Or use your favorite tool that supports PHP and MySQL.

2. Navigate to the `/wp-content/themes` folder in your WordPress installation and clone this repository using `git clone` this repo.

3. Navigate to the cloned repository by running `cd carlifebydani` and install the necessary dependencies by running `npm install`.

4. Log in to your WordPress admin panel, go to Appearance > Themes and activate the 'CarlifebyDani' theme.

## Required Extensions

This project uses Prettier & PHP Intelephense for code formattin. To install Prettier in Visual Studio Code:

1. Open Visual Studio Code.
2. Click on the Extensions view icon on the Sidebar or press `Ctrl+Shift+X`.
3. Search and install the flowing:

-   `Prettier - Code formatter`
-   `PHP Intelephense`

4. Optional you can also install `Tailwind CSS IntelliSense`

## User Settings

After installing the `Prettier - Code formatter` & `PHP Intelephense` plugins for your code editor.

1. Open the command palette in your code editor by pressing `Ctrl + Shift + P`, type `Open User Settings JSON`, and select the command. This will open your `settings.json` file.

2. Add the following code to your `settings.json` file:

```json
{
    "editor.formatOnSave": true,
    "editor.codeActionsOnSave": ["source.formatDocument", "source.fixAll.eslint"],
    "php.format.rules.catchOnNewLine": false,
    "[php]": {
        "editor.defaultFormatter": "bmewburn.vscode-intelephense-client",
        "editor.formatOnSave": true
    },
    "editor.defaultFormatter": "esbenp.prettier-vscode"
}
```

## Development

This theme uses Tailwind CSS. To compile your CSS, run:

```bash
npm run dev
```

## Create template zip package

1.  Update template version the package.json file (optional)
2.  Use command:

```
npm run pack
```

You will have the zip package in project root folder

# Confic and Constants

Constants used throughout the application.

This file contains various constants that are used in different parts of the application.
It is located under the `/constants.php` file.


# References

https://bonnick.dev/posts/tailwind-css-with-wordpress
