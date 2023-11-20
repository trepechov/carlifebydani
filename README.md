# CarLife by Dani WordPress Theme

This is a custom WordPress theme developed by Dani. It's designed for car enthusiasts and uses Tailwind CSS for styling.

## Features

-   Responsive design
-   Custom post types
-   Tailwind CSS for rapid UI development

## Installation
1. To run a local instance of WordPress. Follow the steps below:
  - Download and install [Local WP](https://localwp.com/). Ask someone for backup file.
  - Or use your favorite tool that supports PHP and MySQL.

2. Navigate to the `/wp-content/themes` folder in your WordPress installation and clone this repository using `git clone` this repo.

3. Navigate to the cloned repository by running `cd carlifebydani` and install the necessary dependencies by running `npm install`.

4. Log in to your WordPress admin panel, go to Appearance > Themes and activate the 'CarlifebyDani' theme.


## Required Extensions

This project uses Prettier for code formatting. To install Prettier in Visual Studio Code:

1. Open Visual Studio Code.
2. Click on the Extensions view icon on the Sidebar or press `Ctrl+Shift+X`.
3. Search for `Prettier - Code formatter`.
4. Click on the install button.


## User Settings

1. Install the Prettier - Code formatter plugin for your code editor.

2. Open the command palette in your code editor by pressing `Ctrl + Shift + P`, type `Open User Settings JSON`, and select the command. This will open your `settings.json` file.

3. Add the following code to your `settings.json` file:

````json
{
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "editor.formatOnSave": true,
  "editor.codeActionsOnSave": ["source.formatDocument", "source.fixAll.eslint"],
  
}
````
## Development

This theme uses Tailwind CSS. To compile your CSS, run:

```bash
npm run dev
````

# References
https://bonnick.dev/posts/tailwind-css-with-wordpress
