# onOffice WP-Websites | Timeless Touch

[![Release a final new version](https://github.com/onOffice-Web-Org/onoffice-timeless/actions/workflows/final-release.yml/badge.svg)](https://github.com/onOffice-Web-Org/onoffice-timeless/actions/workflows/final-release.yml)
[![Built with Grunt](https://cdn.gruntjs.com/builtwith.svg)](https://gruntjs.com/)


##  Development

####  Hint: You need <strong>Nodejs</strong> in your WSL (Ubuntu) - (Node.js > v.18)

1. Open your terminal e.g. VSCode integrated Terminal, PHPStorm Terminal. Git Bash, Microsoft Terminal or something else in project directory 
2. run `npm install`: Installs all dependencies.

```
npm install
```

3. Create `.env` file in the root directory (this will be ignored by Git).

```
cp .env
```

4. Set your local WordPress URL in the `.env` file in the following variable


`LOCAL_DEV_URI='http://localhost/your-local-wordpress-project-folder-path'` save and close this file. For instance:

```
LOCAL_DEV_URI='http://localhost/wordpress'
```

5. run `npm run setup` in the console. Before you start your development, you need to build all files. 

```bash
npm run setup
```

6. run `npm start` in the console. Now you can start to work! Open your Browser with the Port number in your console and use Browsersync. 

```bash
npm start
```
  

##  Build

  

Inside this folder, run the following command to create a zip that you can upload to WordPress.

  
1. Compile the files with `npm run build`.

```bash
npm run build 
```

##  Release

  

1. From inside the folder with this `README.md`, generate the zip with the following command. The folder needs to be called `onoffice-pt-orange`.


```bash
npm run release 
```

  

  

##  FRAMEWORKS


- ACF Pro
- ACF Extended Pro
- Splidejs
- Magnific

  
#  Handbook

  
  

##  Override Blocks in your Parent Theme

1. Create the folder: `blocks/your-block` => *look the folder name at `/shared/blocks/oo-example`*

2. Copy the files from the folder `/shared/blocks/example` and edit your `example-render.php` or add your custom styling inside the .scss file.

3.  Add this Code to your function.php into your Parent Theme:

####  Options 
 *(Required) **path** => shared path to shared block folder without file extension  
  (Optional) **override-parent-render** => your render php template  
  (Optional) **override-parent-style** => your ovrride css template (Please create .scss File and start npm task)*  

```php
<?php 

// Override Shared Blocks in your Parent Theme 
add_filter('onoffice_block_setup', function ($blocks) {
	return array_merge($blocks, [
		'oo/your-block-slug' => [
			// Required: Shared Folder Path to your block, that you want to override
			'path' => OO_PARENT_PATH . '/shared/blocks/text', 

			// Required:  Full Path to your override that you want to override. 
			'override-parent-render' =>   OO_PARENT_PATH . '/blocks/text/text-render.php', 

			// Optional: Full Path to your override-styling that you want to override
			'override-parent-style' =>   OO_PARENT_PATH . '/blocks/your-block/your-style.css', 
		],
	]);
});
```
    

  ___

##  Override Blocks in your Child Theme

1. Create the folder: `child-blocks/your-block` => *look the folder name at `/shared/blocks/oo-example`*

2. Copy the files from the folder  `/shared/blocks/oo-example` and edit your `example-render.php` or add your custom styling inside the .scss file.

3. Add this Code to your function.php into your Child Theme:

####  Options 
*(Required) **override-child-render** => your render php template  
(Optional) **override-child-style** => your ovrride css template (Please create .scss File and start npm task)*  

```php
<?php 

// Override Shared Blocks in your Child Theme 
add_filter('onoffice_block_setup', function ($blocks) {
	return array_merge_recursive($blocks, [
		'oo/your-block-slug' => [
			
			// Required:  Full Path to your override that you want to override. 
			'override-child-render' => OO_CHILD_PATH . '/child-blocks/text/render.php'

			// Optional: Full Path to your override-styling that you want to override
			'override-child-style' => OO_CHILD_PATH . '/child-blocks/text/style.css',

		],
	]);
}, 30 ); 
```

---

##  Override Modules for Header & Footer

Module ID List

|  ID  | Modul Name  |
| ------------ | ------------ |
|  contact  |  Adress & Kontaktdaten  |
|  social   |  Social Media |
|  image  |  Bild / Logo |
|  seals |  Siegel  |
|  text  |  Text  |
|  links | Links  |
|  newsletter | Newsletter  |


#### Parent Theme 

1.  Create a folder `parent-modules` in your Parent Theme
2.  Copy your Module File from Example:  `shared/modules/header-social.php` into your Folder  `parent-modules `
3. Write your Code - Use the PHP variable **$args** to get all values from ACF. 

#### Child Theme

1.  Create a folder `child-modules` in your Child Theme
2.  Copy your Module File from Example:  `shared/modules/header-social.php` into your Folder  `child-modules `
3. Write your Code - Use the PHP variable **$args** to get all values from ACF. 



##  Override Templates 

#### Parent Theme 

1.  Create a folder `parent-templates` in your Parent Theme
2.  Copy your Template File from Example:  `shared/templates/header-contact-details.php`  into your Folder  `parent-templates `
3. Write your Code - Use the PHP variable **$args** to get all values from ACF. 

#### Child Theme

1.  Create a folder `child-templates` in your Child Theme
2.  Copy your Template File from Example:  `shared/templates/header-contact-details.php` into your Folder  `child-templates `
3. Write your Code - Use the PHP variable **$args** to get all values from ACF. 

## Update Server

1.  Add this Code to your Parent Theme functions.php

```php
add_filter('oo_theme_updates_data', function ($data) {
    $data['slug'] = 'onoffice-pt-orange';
    $data['json'] = 'your-server.com/update.json';

    return $data;
});
```

2.  Create a folder on your Server and add a file with the File-Extension: .json and paste the following code and edit it.

```plaintext
{
  "version": "your-version-number",
  "details_url": "changelog html page",
  "download_url": "path to zip folder"
}
```
