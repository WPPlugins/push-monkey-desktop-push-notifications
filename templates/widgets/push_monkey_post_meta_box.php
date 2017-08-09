<div class="preview-container">
  <?php if( ! $account_key ) { ?>
  <div class="error-message">
    <p>Sign In before you can use Push Monkey. Don't have an account yet? <a href="<?php echo $register_url; ?>">Click here to Sign Up</a>. <a href="http://www.getpushmonkey.com/help?source=plugin#gpm4">More info about this</a>.
    </p>
  </div>
  <?php } ?>
  <h4>Notification Preview</h4>
  <div class="notification">

  <img class="icon" src="<?php echo $this->endpointURL; ?>/clients/icon/<?php echo $account_key; ?>" />   

    <p>
      <strong id="push_monkey_preview_title"><?php echo $title; ?></strong> 
      <br /> 
      <span id="push_monkey_preview_content"><?php echo $body; ?></span>
    </p>

  </div>
</div>
<input type="checkbox" id="push_monkey_opt_out" name="push_monkey_opt_out" <?php echo $disabled; ?> <?php echo $checked; ?> />
<label for="push_monkey_opt_out">Don't send push notification for this post</label>
<?php if ( count($segments) ) { ?>
  
  <h3>Segments</h3>
  <strong>NOTE: </strong><span> Leave un-checked to send to all subscribers.</span>
  <a href="http://www.getpushmonkey.com/help#gpm22">Help? &#8594;</a>
  <br/>
  <?php foreach( $segments as $segment ) { ?>
    <? foreach ( $segment as $k => $v ) { ?>
      <input type="checkbox" name="push_monkey_post_segments[]" value="<?php echo $k; ?>" id="push_monkey_post_segments_<?php echo $k; ?>" />
      <label for="push_monkey_post_segments_<?php echo $k; ?>"><?php echo $v; ?></label>
      <br/>
    <?php } ?>  
  <?php } ?>  
<?php } ?>
<p class="howto">
  Disabling push notifications doesn't send notifications even if the marked post category normally does. <a href="http://www.getpushmonkey.com/help#gpm9">Help? &#8594;</a>
</p>