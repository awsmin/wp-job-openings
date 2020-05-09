## Changelog

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
