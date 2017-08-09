<div class="container">
	<?php if( $posted ) { ?>
		<div class="updated-message"><p>Push Notification Sent. Yay!</p></div>
	<?php } ?>
	<?php if(!$account_key) { ?>
	<div class="error-message"> 
		<p>
			Sign in before you can use Push Monkey. Don't have an account yet? 
			<a href="<?php echo $settings_url; ?>">Click here to sign up</a>. 
			<a href="http://www.getpushmonkey.com/help?source=plugin#gpm16" target="_blank">More info about this &#8594;</a>
		</p>
	</div>
	<?php } ?>
	<form method="post" enctype="multipart/form-data">
		<div class="row">
			<label for="pm_title">
				Title
				<span>of the push message. 25 characters or less.</span>
			</label>
			<input type="text" class="regular-text" name="title" id="pm_title" maxlength="25"/>
		</div>
		
		<div class="row">
			<label for="pm_message">
				Message
				<span>120 characters or less.</span>
			</label>
			<textarea class="regular-text" name="message" maxlength="120" id="pm_message"></textarea>
		</div>

		<div class="row">
			<label for="pm_url">
				URL
				<span>Where the reader will land after clicking on the notification.</span>
			</label>
			<input type="text" class="regular-text" maxlength="100" id="pm_url" name="url"/>
		</div>

		<div class="row">
			<label for="pm_image">
				Image (optional)
				<span>To be displayed with the message. <a href="https://blog.getpushmonkey.com/2017/04/notifications-with-images/">More about images.</a></span>
			</label>
			<input type="file" class="regular-text" name="image" id="pm_image"/>
		</div>		

		<div class="row">
			<?php if ( count($segments) > 0 ) {?>
				<h3>Segments</h3>
			  <?php foreach( $segments as $segment ) { ?>
					<? foreach ( $segment as $k => $v ) { ?>
					  <input type="checkbox" id="push_monkey_segment_<?php echo $k; ?>" name="push_monkey_post_segments[]" value="<?php echo $k; ?>" />
					  <label class="for_checkbox" for="push_monkey_segment_<?php echo $k; ?>"><?php echo $v; ?></label>
					  <br/>
					<?php } ?>
			  <?php } ?>		
			<?php } else { ?>
				<p>
					<strong>OPTIONAL TIP:</strong> You have not set up any segments. 
					<a href="http://www.getpushmonkey.com/help#gpm22" target="_blank">What are segments?</a>
				</p>
			<?php } ?>
		</div>

		<input type="hidden" name="push_monkey_push_submit" value="1" />

		<div class="row">
			<a class="button button-primary" rel="leanModal" href="#push_monkey_confirmation_modal">Send</a>
		</div>
	</form>
</div>

<!-- Confirmation Modal -->
<div class="push_monkey_modal" id="push_monkey_confirmation_modal" style="display:none;">
	<div class="push_monkey_modal_inner">
		<p>
			Are you sure that you want to send this custom desktop push notification?
		</p>
	</div>
	<div class="push_monkey_modal_footer">
		<a class="button button-secondary close_modal" href="javascript:void(0);" >No</a>
		<a class="button button-primary push_monkey_submit" href="javascript:void(0);" >Yes. Send it.</a>
	</div>
</div>
