<?php
/**
 * Title: Contact Layout
 * Slug: crann-gra-theme/page-contact
 * Categories: pages
 */
?>
<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"60px","bottom":"60px"}}}} -->
<div class="wp-block-group" style="padding-top:60px;padding-bottom:60px">
	<!-- wp:heading {"level":1,"style":{"color":{"text":"var(--wp--preset--color--primary)"},"typography":{"fontSize":"48px"}}} -->
	<h1 class="wp-block-heading" style="color:var(--wp--preset--color--primary);font-size:48px">Contact Us</h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">We’d love to hear from you! Send us a message or call us.</p>
	<!-- /wp:paragraph -->

	<!-- wp:spacer {"height":"30px"} -->
	<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":2} -->
			<h2 class="wp-block-heading">Direct Contact</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><strong>Email:</strong> <a href="mailto:hello@cranngra.ie">hello@cranngra.ie</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:spacer {"height":"40px"} -->
	<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
	<!-- /wp:spacer -->

	<!-- wp:group {"layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"30px","right":"30px","bottom":"30px","left":"30px"}},"border":{"width":"1px","style":"solid","color":"#e0dedc"}}} -->
	<div class="wp-block-group" style="border-style:solid;border-width:1px;border-color:#e0dedc;padding-top:30px;padding-right:30px;padding-bottom:30px;padding-left:30px">
		<!-- wp:heading {"level":2} -->
		<h2 class="wp-block-heading">Send a Message</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph -->
		<p>Have a question about what plants are in stock, or need advice on what will grow in your garden? Fill out the form below and we'll get back to you as soon as we wash the soil off our hands.</p>
		<!-- /wp:paragraph -->
		
		<!-- wp:shortcode -->
		[contact-form-7 title="Contact form 1"]
		<!-- /wp:shortcode -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
