## Changelog

### V 3.4.4 - 2023-02-06
* Fixed: Structure breaks when job listing shortcode in job detail page.
* Minor bug fixes and code improvements.

### V 3.4.2 - 2023-08-22
* Minor bug fixes and code improvements.

### V 3.4 - 2023-07-31
* Added: Job expiry notification.
* Fixed: Responsive toggle issue.
* Fixed: Unable to delete expired jobs on the job edit page.
* Improved: Expired post states added in admin listings.
* Minor bug fixes and code improvements.

### V 3.4.1 - 2023-08-01
* Bug fixes

### V 3.3.3 - 2022-11-02
* Fixed: Failed to open directory issue in Add-ons screen.
* Improved: Notifications template tags.
* Dev: Hooks for dashboard and overview data customization.
* Minor bug fixes and code improvements.

### V 3.3.2 - 2022-10-19
* Fixed: Job specifications settings issue when options with similar words are entered.
* Fixed: HTML content issue in notification mails for some installations.
* Improved: Mail notification template. Logo in mail notification with the link to the site homepage.

### V 3.3.1 - 2022-07-06
* Fixed: Uploading issue with documents exported with Google.
* Fixed: Accessibility issue in job listing filters.
* Fixed: Deprecation notice with function wp_no_robots.
* Improved: Settings error handling.
* Dev: Added functions for better debugging.
* Minor bug fixes and code improvements.

### V 3.3.0 - 2022-04-25
* Added: HTML editor support for notifications.
* Added: Author info in the admin job listing table.
* Fixed: HTML structure issue in the notification mail.
* Improved: Notifications mail handling.
* Improved: Multilingual support for job specifications.
* Dev: Hooks for specifications customization.
* Code improvements.

### V 3.2.1 - 2022-02-02
* Fixed: Search field style issues in job listing.
* Fixed: Responsive style issues with job filters.

### V 3.2.0 - 2022-01-31
* WordPress 5.9 compatibility fixes.
* Added: Akismet Anti-Spam Protection.
* Fixed: Application form issue with in-app browsers.
* Improved: Job listing filters UI.
* Other minor bug fixes and style improvements.

### V 3.1.0 - 2021-12-22
* Added: Option to enable plugin-based form styles (Settings > Form > General > Form Style). Job listing and detail page templates need to be updated if overridden in the theme.
* Fixed: Warnings in job archive pages.
* Fixed: Notifications translations not working with Polylang plugin.
* Dev: Hook to customize the wrapping element class for the job listing and detail page.
* Code improvements.

### V 3.0.0 - 2021-12-03
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

### V 2.3.1 – 2021-10-28
* Fixed: Media missing from library in WordPress.com when 'Secure uploaded files' option is enabled.
* Fixed: Accessibility issues in job filters. #28
* Fixed: GDPR text issue in Polylang when accents are used.

### V 2.3.0 – 2021-09-16
* Added: Timezone setting for job expiration.
* Added: Setting to remove custom permalink front base.
* Fixed: Slash issue in the mail with special characters.
* Improved: Updated jQuery Validation Plugin to version 1.19.3.
* Dev: Added new filter hooks to control the plugin-generated UI and content.
* Dev: Improved Form Handling Hooks.
* Dev: Deprecated 'awsm_specification_content' filter hook in favor of 'awsm_job_specs_content'.
* Code improvements.
* Other minor bug fixes.

### V 2.2.0 – 2021-06-09
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

### V 2.1.1 – 2021-04-21
* Fixed: Issue with WooCommerce Plugin that prevents the users with HR Role from accessing the backend.
* Fixed: 'Secure uploaded files' option doesn't work in 'Media Library' for some installations.
* Fixed: Job Filters not working for some installations.
* Improved: Redirect users with HR Role to job page instead of profile page after login.
* Improved: WPML compatibility for Settings.
* Other minor bug fixes and style improvements.

### V 2.1.0 – 2020-12-08
* WordPress 5.6 compatibility fixes.
* Added: Featured image support for Job Openings and in the job listing and an option to enable the support. Template files need to be updated if overridden in theme.
* Added: Excerpt, Author, and Custom fields support for Job Openings.
* Added: Force expiry option in the submit meta box.
* Added: Option to disable the archive page for Job Openings.
* Improved: Date and Time formatting.
* Code improvements and minor bug fixes.

### V 2.0.0 - 2020-05-01
* Admin UI improvements.
* Added: New Onboarding interface.
* Added: Job Overview dashboard widget.
* Added: Custom Admin navigation.
* Added: HTML Template support for notification mails.
* Added: Daily email digest if there are new applications.
* Added: Drag and Drop sorting for Job Specifications.
* Added: Functionality to clear searched value.
* Added: 'Actions' meta box in application edit screen.
* Added: Next and Previous navigation in application edit screen.
* Added: Reply-To support in Admin notification mail.
* Fixed: An issue that prevents user from adding numeric values for job specification in job edit screen.
* Fixed: Job detail page returning empty content in some themes.
* Fixed: Specification terms not removing from settings if it contains some special characters.
* Dev: New hooks for customizing the form fields.
* Dev: New hook for customizing the terms display in job specification filters.
* Other minor fixes and code improvements.

### V 1.6.2 - 2020-01-29
* Bug fixes and improvements.

### V 1.6.1 - 2020-01-14
* Fixed: Job search results showing invalid listings when 'Load more' button is clicked.
* Fixed: An issue that prevents user from closing the job specification dropdown.
* Fixed: Job specification based translations not working in WPML.
* Other minor fixes and improvements.

### V 1.6.0 - 2019-12-31
* Added: Jobs Search
* Added: Option to hide and restrict files uploaded through application form.
* Added: Ability to add 'From' mail address for Admin notifications in settings.
* Added: WPML Support.
* Improved: Job archive page title.
* Dev: Added hooks for customizing the meta box content.
* Dev: Added hooks for customizing shortcode attributes and content.
* Code improvements and minor bug fixes.

### V 1.5.1 - 2019-11-08
* Fixed: Job application-related attachments security issue in some installations.
* Fixed: Unable to dismiss the admin notices.

### V 1.5.0 - 2019-10-26
* Added: Ability to add 'From' and 'Reply-To' mail addresses for Applicant notifications in settings.
* Fixed: Select2 library compatibility issues with other plugins.
* Fixed: Application submission issue in Internet Explorer.
* Fixed: Upload file extensions empty state issue in settings.

### V 1.4.2 - 2019-08-08
* Minor bug fixes.

### V 1.4.1 - 2019-07-02
* Minor bug fixes and style improvements.

### V 1.4.0 - 2019-06-12
* Added: Recent Jobs Widget.
* Added: Application ID template tag support for Application Notifications.
* Fixed: Job application notification mail delivery issues.
* Fixed: Job expiry datepicker button styling issues.
* Fixed: Required fields in sub tabs preventing settings form submission.
* Improved: Settings page functionality.
* Improved: Job application form validation styles.
* Improved: Add-ons listing page.
* Code improvements and other bug fixes.
* Dev: New hooks for customizing the registered post type arguments.
* Dev: New hook for customizing the arguments for the jobs query.
* Dev: New hooks for managing job application notification mails.

### V 1.3.0 - 2019-03-04
* Added: Shortcode attributes - `filters`, `listings`, and `loadmore` for `[awsmjobs]` shortcode. Template files need to be updated if overridden in theme.
* Added: Job/Application updated messages.
* Added: Shareable filters. Now, you can share the link to display filtered job results.
* Added: New hooks for customizing Application Form. Template files need to be updated if overridden in theme.
* Added: Specification Key option to Job Specifications settings.
* Added: Jetpack publicize feature support.
* Fixed: Conflict with Polylang plugin.
* Fixed: Shortcode returning blank screen with some page builder plugins. Template files need to be updated if overridden in theme.
* Fixed: Job specification settings validation issues.
* Fixed: Localization issues.
* Improved: Templating for Job Specifications settings based on Underscore.js.
* Other bug fixes and code improvements.

### V 1.2.1 - 2018-11-16
* Fixed: Job Application submission error when caching plugin is used
* Fixed: Application feedback mail issue when Non-English characters are used
* Fixed: Settings UI issues

### V 1.2.0 - 2018-10-17
* Added: Job posting structured data for better SEO
* Added: New hooks for managing job application form
* Fixed: Validation issue for Full Name field in job application form
* Improved: Functionality of the Settings page
* Other minor fixes and improvements

### V 1.1.2 - 2018-09-23
* Fixed: Plugin activation error due to conflict with other plugins, especially Yoast SEO

### V 1.1.1 - 2018-09-21
* Fixed: Plugin activation is terminated when plugin is activated through WP-CLI
* Fixed: Invalid error messages when reCAPTCHA is not enabled in Form settings
* Code improvements

### V 1.1.0 - 2018-09-12
* Added: Custom template support.
* Added: New hooks for better templating
* Added: reCAPTCHA feature for job application form
* Added: New job specifications display option on job detail page appearance settings
* Fixed: Job view count issue when caching plugin is used
* Minor bug fixes
* Overall code and performance improvement

### V 1.0.1 - 2018-08-22
* Added: Job specifications display options on job detail page appearance settings
* Added: Job status meta box on application detail screen
* Minor fixes and improvements

### V 1.0.0 - 2018-08-12
* Initial Release
