<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="awsm-job-welcome-message"><?php esc_html_e( "Getting started is easy! We put together this quick start guide to help first time users of the plugin. Our goal is to get you up and running in no time. Let's begin!", 'wp-job-openings' ); ?></p>

<div id="awsm-getting-started" class="awsm-tab-item">
	<div class="awsm-welcome-points">
		<div class="awsm-row awsm-welcome-point">
			<div class="awsm-col awsm-col-half">
				<div class="awsm-welcome-point-image">
					<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/settings.gif' ); ?>" alt="">
				</div><!-- .awsm-welcome-point-image -->
			</div><!-- .col-->
			<div class="awsm-col awsm-col-half">
				<div class="awsm-welcome-point-content">
					<h2><?php esc_html_e( 'Step 1: Configure the settings', 'wp-job-openings' ); ?></h2>
					<p><?php esc_html_e( 'Well, there is nothing to be worried there. You just hve to setup some basic stuff like, filling in the company name, HR email address and all. You can come back there later and make modifications as you like.', 'wp-job-openings' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=awsm_job_openings&page=awsm-jobs-settings' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Go to Settings', 'wp-job-openings' ); ?></a>
				</div><!-- .awsm-welcome-point-content -->
			</div><!-- .col-->
		</div><!-- .row -->
		<div class="awsm-row awsm-welcome-point">
			<div class="awsm-col awsm-col-half">
				<div class="awsm-welcome-point-image">
					<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/create.gif' ); ?>" alt="">
				</div><!-- .awsm-welcome-point-image -->
			</div><!-- .col-->
			<div class="awsm-col awsm-col-half">
				<div class="awsm-welcome-point-content">
					<h2><?php esc_html_e( 'Step 2: Create your first Job Opening', 'wp-job-openings' ); ?></h2>
					<p><?php esc_html_e( 'Super straight-forward. It’s like any other post, with the extra fields only you require. The job specs can be created right from the job creation page itself and the values are resuable. The best part? You can make search filters out of any job spec you add. No limitation, at all!', 'wp-job-openings' ); ?></p>
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=awsm_job_openings' ) ); ?>" class="button button-primary button-large"><?php esc_html_e( 'Create a Job Opening', 'wp-job-openings' ); ?></a>
				</div><!-- .awsm-welcome-point-content -->
			</div><!-- .col-->
		</div><!-- .row -->
		<div class="awsm-row awsm-welcome-point">
			<div class="awsm-col awsm-col-half">
				<div class="awsm-welcome-point-image">
					<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/rocket.gif' ); ?>" alt="">
				</div><!-- .awsm-welcome-point-image -->
			</div><!-- .col-->
			<div class="awsm-col awsm-col-half">
				<div class="awsm-welcome-point-content">
					<h2><?php esc_html_e( 'Step 3: Start hunting talents!', 'wp-job-openings' ); ?></h2>
					<p><?php esc_html_e( 'That’s literally it! Go ahead and start exploring the settings evein more once you are done adding the job openings. Even the first version of the plugin comes with most of the features you would need to setup a careers page. ', 'wp-job-openings' ); ?></p>
				</div><!-- .awsm-welcome-point-content -->
			</div><!-- .col-->
		</div><!-- .row -->
	</div><!-- .awsm-welcome-points -->
	<div class="more-awsm-plugins">
		<h2><?php esc_html_e( 'More plugins from our house!', 'wp-job-openings' ); ?></h2>
		<div class="awsm-row">
			<div class="awsm-col awsm-col-half">
				<a href="https://goo.gl/A8Rmxn" class="awsm-plugin-item" target="_blank">
					<div class="awsm-plugin-item-inner">
						<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/team.png' ); ?>" alt="">
						<div class="awsm-plugin-item-info">
							<h3><?php esc_html_e( 'The Team Pro', 'wp-job-openings' ); ?></h3>
							<p><?php esc_html_e( 'The most versatile WordPress plugin available to create and manage your Team page.', 'wp-job-openings' ); ?></p>
						</div>
					</div>
				</a>
			</div><!-- .col -->
			<div class="awsm-col awsm-col-half">
				<a href="http://goo.gl/wJTQlc" class="awsm-plugin-item" target="_blank">
					<div class="awsm-plugin-item-inner">
						<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/ead.png' ); ?>" alt="">
						<div class="awsm-plugin-item-info">
							<h3><?php esc_html_e( 'Embed Any Document Plus', 'wp-job-openings' ); ?></h3>
							<p><?php esc_html_e( 'With just one click you can easily embed your Google Docs files and documents hosted in DropBox & Box.com to your WordPress website.', 'wp-job-openings' ); ?></p>
						</div>
					</div>
				</a>
			</div><!-- .col -->
			<div class="awsm-col awsm-col-half">
				<a href="http://goo.gl/1emFRf" class="awsm-plugin-item" target="_blank">
					<div class="awsm-plugin-item-inner">
						<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/dropr.png' ); ?>" alt="">
						<div class="awsm-plugin-item-info">
							<h3><?php esc_html_e( 'Dropr - Dopbox Plugin', 'wp-job-openings' ); ?></h3>
							<p><?php esc_html_e( 'Dropr lets you access files from your Dropbox account and help you to add them straight to your WordPress website. Securely and safely.', 'wp-job-openings' ); ?></p>
						</div>
					</div>
				</a>
			</div><!-- .col -->
			<div class="awsm-col awsm-col-half">
				<a href="https://goo.gl/y4Uf2w" class="awsm-plugin-item" target="_blank">
					<div class="awsm-plugin-item-inner">
						<img src="<?php echo esc_url( AWSM_JOBS_PLUGIN_URL . '/assets/img/drive.png' ); ?>" alt="">
						<div class="awsm-plugin-item-info">
							<h3><?php esc_html_e( 'Drivr - Google Drive Plugin', 'wp-job-openings' ); ?></h3>
							<p><?php esc_html_e( 'Drivr helps you to add files from your Google Drive to your WordPress site quickly and seamlessly.', 'wp-job-openings' ); ?></p>
						</div>
					</div>
				</a>
			</div><!-- .col -->
		</div><!-- .row -->
	</div><!-- .more-awsm-plugins -->
</div><!-- .awsm-tab-item -->
