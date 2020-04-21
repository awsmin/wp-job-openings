<?php
/**
 * Footer template part for mail.
 *
 * Override this by copying it to currenttheme/wp-job-openings/mail/footer.php
 *
 * @package wp-job-openings
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
						</td>
					</tr><!-- end tr -->
					<tr>
						<td valign="middle" class="footer email-section">
							<table style="width: 100%;">
								<tr>
									<td style="text-align: center; padding: 38px 0 50px;">
										<p style="margin: 0;"><?php printf( esc_html__( 'Sent from %s by WP Job Openings Plugin', 'wp-job-openings' ), '<a href="{site-url}">{site-title}</a>' ); ?></p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</center>
	</body>
</html>
