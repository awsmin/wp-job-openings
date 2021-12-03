[![WP Job Openings](https://ps.w.org/wp-job-openings/assets/banner-772x250.png?rev=2296666)](https://wordpress.org/plugins/wp-job-openings/)
# WP Job Openings
**Contributors:** awsmin  
**Tags:** jobs, job listing, job openings, job board, careers page, jobs page, wp job opening, jobs plugin  
**Requires at least:** 4.8  
**Tested up to:** 5.8  
**Requires PHP:** 5.6  
**Stable tag:** 3.0.0  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  
**Donate link:** https://www.buymeacoffee.com/awsm

## Summary

Super simple Job Listing plugin to manage Job Openings and Applicants on your WordPress site.

## Description

**WP Job Openings plugin is the most simple yet powerful plugin for setting up a job listing page for your WordPress website.**

WP Job Openings is designed after carefully analysing hundreds of job listing layouts and methods. The plugin is super simple to use and extensible to a high performing recruitment tool.

The plugin comes with two layouts - Grid and List which are designed carefully according to the modern design and User Experience principles. Highlight of the plugin is its totally flexible filter options.


**[View Demo](https://demo.wpjobopenings.com/)**

**[Visit website - wpjobopenings.com](https://wpjobopenings.com/)**


## Key Features

* Super Simple and Easy to Set Up and Use
* Two Different Modern Layouts
* Clean and User Friendly Designs
* Unlimited Job Specifications
* Unlimited Filtering Options
* Search Option to find jobs
* AJAX Powered Job Listing and Filtering
* Comes with Default Form to Submit Applications
* HR Role for setting up HR user
* Options to Customise Email Notifications
* Custom Email Notification Templates
* Application Listings in Plugin
* Job Expiry Options
* Job posting structured data for better SEO
* Recent Jobs Widget
* WPML Support
* Developer Friendly (Lots of hooks!)
* Detailed Documentation
* Tested with more than 50 top WordPress themes and Plugins

## Add-ons

* [Docs Viewer](https://wordpress.org/plugins/docs-viewer-add-on-for-wp-job-openings/) (FREE)
* [Auto-Delete Applications for GDPR Compliance](https://wordpress.org/plugins/auto-delete-applications-add-on-for-wp-job-openings/) (FREE)
* [PRO Pack](https://wpjobopenings.com/pro-pack/) (PREMIUM)

## WP Job Openings PRO Features

**Power-up your job listing with the PRO pack Add-on**

* Form Builder - Make your own application form
* Shortlist, Reject and Select Applicants
* Rate and Filter Applications
* Custom Email Notifications & Templates
* Email CC option for job submission notifications
* Notes and Activity Log
* Option to Filter and Export Applications
* Attach uploaded file with email notifications
* Shortcode generator for generating customised job lists
* Use third-party forms and custom application URLs


**[Get PRO Pack](https://wpjobopenings.com/pro-pack/)**

## Contribute

**You can contribute to the community by translating the plugin to your language.** Believe us, it's super-easy. Click on the link below, choose your language and start translating the strings in Development (trunk).

* **[Translate plugin to your language](https://translate.wordpress.org/projects/wp-plugins/wp-job-openings/)**

## Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the `Plugins` screen in WordPress

## Screenshots

<img src="https://ps.w.org/wp-job-openings/assets/screenshot-5.jpg?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-4.png?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-9.jpg?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-6.png?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-8.jpg?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-11.png?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-1.png?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-2.png?rev=2296666" width="30%"></img> <img src="https://ps.w.org/wp-job-openings/assets/screenshot-3.png?rev=2296666" width="30%"></img> 

## Changelog

**V 3.0.0 - 2021-12-03**
* Admin UI improvements.
* Job listing UI improvements. The template file needs to be updated if overridden in the theme.
* Added: Overview page with support for Applications Analytics widget, Get Started widget, Recent Applications widget, Open Positions widget, and Your Listings widget.
* Added: Multiple pagination support - Classic or Modern.
* Fixed: 'Add New' button for applications being displayed for Multisite network.
* Fixed: Issue in removing duplicate job specification options.
* Fixed: Email digest from address is not the same as the mail address for admin notification.
* Improved: Admin dashboard widget.
* Improved: Job Specifications settings.
* Improved: HR user capabilities.
* Dev: Deprecated job listing hooks.
* Dev: Deprecated recent jobs widget hooks.
* Dev: Hook to override the allowed HTML for the form.
* Dev: Hook to customize expired job content.
* Code improvements.
* Other minor bug fixes.

**V 2.3.1 – 2021-10-28**
* Fixed: Media missing from library in WordPress.com when 'Secure uploaded files' option is enabled.
* Fixed: Accessibility issues in job filters. #28
* Fixed: GDPR text issue in Polylang when accents are used.

**V 2.3.0 – 2021-09-16**
* Added: Timezone setting for job expiration.
* Added: Setting to remove custom permalink front base.
* Fixed: Slash issue in the mail with special characters.
* Improved: Updated jQuery Validation Plugin to version 1.19.3.
* Dev: Added new filter hooks to control the plugin-generated UI and content.
* Dev: Improved Form Handling Hooks.
* Dev: Deprecated 'awsm_specification_content' filter hook in favor of 'awsm_job_specs_content'.
* Code improvements.
* Other minor bug fixes.

**V 2.2.0 – 2021-06-09**
* Added: Notification Mail Template Customizer (Settings > Notifications > Customize). Template files need to be updated if overridden in the theme.
* Fixed: Pre validation for file field not working.
* Fixed: Cover letter formatting issue in the notification mail content.
* Fixed: Accents don't work in the Specification fields.
* Improved: Form Handling. Template files need to be updated if overridden in the theme.
* Improved: Notification HTML Mail Template.
* Dev: New Hooks for Handling Job Filters.
* Dev: Improved Form Handling Hooks.
* Dev: Added JS Events to handle Form Submission, Filters, and Load More.
* Other minor bug fixes and style improvements.

**V 2.1.1 – 2021-04-21**
* Fixed: Issue with WooCommerce Plugin that prevents the users with HR Role from accessing the backend.
* Fixed: 'Secure uploaded files' option doesn't work in 'Media Library' for some installations.
* Fixed: Job Filters not working for some installations.
* Improved: Redirect users with HR Role to job page instead of profile page after login.
* Improved: WPML compatibility for Settings.
* Other minor bug fixes and style improvements.

View complete Changelog in [CHANGELOG.MD](https://github.com/awsmin/wp-job-openings/blob/master/CHANGELOG.md)
