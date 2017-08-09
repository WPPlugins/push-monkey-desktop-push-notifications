/*
 * Version: 1.2
 */

var PushMonkeyWPConfig = {};
PushMonkeyWPConfig.endPoint = push_monkey_locals.endpoint_url + '/push'; // DO NOT CHANGE!
PushMonkeyWPConfig.websiteID = push_monkey_locals.website_push_id; 
PushMonkeyWPConfig.name = push_monkey_locals.website_name;

var PushMonkeyWPLog = {};
PushMonkeyWPLog.log = function (msg) {

	// console.log(msg);
}

var PushMonkeyWPAlert = {};
PushMonkeyWPAlert.confirmation = function () {
	alert('You have succesfully subscribed to Safari Push Notifications from ' + PushMonkeyWPConfig.name);
}

var PushMonkeyWP = {};
PushMonkeyWP.isPushEnabled = false;

PushMonkeyWP.register = function () {
	var checkRemotePermission = function (permissionData) {
		if (permissionData.permission === 'default') {
			PushMonkeyWPLog.log('This is a new web service URL and its validity is unknown.');
			window.safari.pushNotification.requestPermission(
				PushMonkeyWPConfig.endPoint, 
				PushMonkeyWPConfig.websiteID, 
				{}, 
				checkRemotePermission 
			);
		} else if (permissionData.permission === 'denied') {
			PushMonkeyWPLog.log('The user said no.');
		} else if (permissionData.permission === 'granted') {
			PushMonkeyWPLog.log('The web service URL is a valid push provider, and the user said yes.');
			PushMonkeyWPAlert.confirmation();
		}
	};

	var permissionData = window.safari.pushNotification.permission(PushMonkeyWPConfig.websiteID); 
	checkRemotePermission(permissionData);
}

PushMonkeyWP.hasServiceWorkers = function() {

    if ('serviceWorker' in navigator) {

      return true;
    }
    return false;
}

PushMonkeyWP.showWrongBrowserBanner = function() {

}

PushMonkeyWP.endpointWorkaround = function(pushSubscription) {
  // Make sure we only mess with GCM
  if (pushSubscription.endpoint.indexOf('https://android.googleapis.com/gcm/send') !== 0) {

    return pushSubscription.endpoint;
  }

  var mergedEndpoint = pushSubscription.endpoint;
  // Chrome 42 + 43 will not have the subscriptionId attached
  // to the endpoint.
  if (pushSubscription.subscriptionId &&
    pushSubscription.endpoint.indexOf(pushSubscription.subscriptionId) === -1) {
    // Handle version 42 where you have separate subId and Endpoint
    mergedEndpoint = pushSubscription.endpoint + '/' +
      pushSubscription.subscriptionId;
  }
  return mergedEndpoint;
}

PushMonkeyWP.sendSubscriptionToServer = function(subscription) {

  // For compatibly of Chrome 43, get the endpoint via
  // endpointWorkaround(subscription)
  // e.g.
  var mergedEndpoint = this.endpointWorkaround(subscription);
  var url = push_monkey_locals.endpoint_url + "/push/v1/register/" + push_monkey_locals.account_key;
  PushMonkeyWPLog.log("register at: ")
  PushMonkeyWPLog.log(url);
  jQuery.ajax({
        type: "POST",
        url: url,
        crossDomain: true,
        data: jQuery.param({"endpoint": mergedEndpoint}),
        success: function (data) {

          PushMonkeyWPLog.log("saved: ");
          PushMonkeyWPLog.log(data);          
        },
        error: function (err) {

          PushMonkeyWPLog.log("error: ");
          PushMonkeyWPLog.log(error);
        }
  });
}

PushMonkeyWP.deleteSubscriptionFromServer = function(subscription) {
  
  // For compatibly of Chrome 43, get the endpoint via
  // endpointWorkaround(subscription)
  // e.g.
  var endpointSections = subscription.endpoint.split('/');
  var subscriptionId = endpointSections[endpointSections.length - 1];
  var url = push_monkey_locals.endpoint_url + "/push/v1/unregister/" + subscriptionId;
  PushMonkeyWPLog.log("unregister at: ", url);
  jQuery.ajax({
        type: "GET",
        url: url,
        crossDomain: true,
        success: function (data) {

          PushMonkeyWPLog.log("saved: ", data);
        },
        error: function (err) {

          PushMonkeyWPLog.log("error: ", error);
        }
  });
}

PushMonkeyWP.subscribe = function() {

  // var pushButton = document.querySelector('.js-push-button');
  // pushButton.disabled = true;
  var button = jQuery('.pm-subscribe-button');
  button.prop('disabled', true);
  var pm = this;
  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    serviceWorkerRegistration.pushManager.subscribe({userVisibleOnly: true})
      .then(function(subscription) {

        pm.isPushEnabled = true;
        jQuery.noty.closeAll();
        return pm.sendSubscriptionToServer(subscription);
      })
      .catch(function(e) {
        if (Notification.permission === 'denied') {
          // The user denied the notification permission which
          // means we failed to subscribe and the user will need
          // to manually change the notification permission to
          // subscribe to push messages
          PushMonkeyWPLog.log('Permission for Notifications was denied');
          button.prop('disabled', true);
        } else {
          // A problem occurred with the subscription, this can
          // often be down to an issue or lack of the gcm_sender_id
          // and / or gcm_user_visible_only
          PushMonkeyWPLog.log('Unable to subscribe to push.', e);
          button.prop('disabled', false);
          button.text('Subscribe');
        }
      });
  });
}

PushMonkeyWP.unsubscribe = function() {

  var button = jQuery('.pm-subscribe-button');
  button.prop('disabled', true);
  var pm = this;
  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    // To unsubscribe from push messaging, you need get the
    // subcription object, which you can call unsubscribe() on.
    serviceWorkerRegistration.pushManager.getSubscription().then(

      function(pushSubscription) {
        // Check we have a subscription to unsubscribe
        if (!pushSubscription) {
          // No subscription object, so set the state
          // to allow the user to subscribe to push
          button.prop('disabled', false);
          button.text('Subscribe');
          pm.isPushEnabled = false;
          return;
        }

        // TODO: Make a request to your server to remove
        // the users data from your data log so you
        // don't attempt to send them push messages anymore

        // We have a subcription, so call unsubscribe on it
        pm.deleteSubscriptionFromServer(pushSubscription);
        pushSubscription.unsubscribe().then(function(successful) {
          // pushButton.disabled = false;
          // pushButton.textContent = 'Enable Push Messages';
          // alerUnsubscribed();
          pm.isPushEnabled = false;
          button.prop('disabled', false);
          button.text('Subscribe');
        }).catch(function(e) {
          // We failed to unsubscribe, this can lead to
          // an unusual state, so may be best to remove
          // the subscription id from your data log and
          // inform the user that you disabled push

          PushMonkeyWPLog.log('Unsubscription error: ', e);
          button.prop('disabled', false);
          pm.isPushEnabled = false;
          button.text('Subscribe');
        });
      }).catch(function(e) {
        PushMonkeyWPLog.log('Error thrown while unsubscribing from push messaging.', e);
      });
  });
}

PushMonkeyWP.initialiseState = function() {

  var pm = PushMonkeyWP;
  // Are Notifications supported in the service worker?
  if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
    PushMonkeyWPLog.log('Notifications aren\'t supported.');
    return;
  }

  // Check the current Notification permission.
  // If its denied, it's a permanent block until the
  // user changes the permission
  if (Notification.permission === 'denied') {
    PushMonkeyWPLog.log('The user has blocked notifications.');
    return;
  }

  // Check if push messaging is supported
  if (!('PushManager' in window)) {

    PushMonkeyWPLog.log('Push messaging isn\'t supported.');
    return;
  }
  PushMonkeyWPLog.log("initialiseState = all is fine")
  // We need the service worker registration to check for a subscription
  navigator.serviceWorker.ready.then(function(serviceWorkerRegistration) {
    // Do we already have a push message subscription?
    PushMonkeyWPLog.log("initialiseState = serviceWorker ready")
    serviceWorkerRegistration.pushManager.getSubscription()
      .then(function(subscription) {
        PushMonkeyWPLog.log("initialiseState = serviceWorker getSubscription")
        // Enable any UI which subscribes / unsubscribes from
        // push messages.
        // var pushButton = document.querySelector('.js-push-button');
        // pushButton.disabled = false;
        PushMonkeyWPLog.log("initialiseState = we might have a subscription")
        if (!subscription) {

          pm.isPushEnabled = false;
          pm.showSubcriptionBanner();
          var button = jQuery('.pm-subscribe-button');
          button.prop('disabled', false);
          button.text("Subscribe");
          button.on("click", function() {

            pm.subscribe();
          });
        } else {

          pm.isPushEnabled = true;
        }
      })
      .catch(function(err) {
        PushMonkeyWPLog.log('Error during getSubscription()', err);
      });
  })
  .catch(function(err) {

    PushMonkeyWPLog.log("Error activating service worker", err);
  });
}

PushMonkeyWP.activateSubscription = function() {

    var path = '/service_worker.php?k='+ push_monkey_locals.account_key;
    navigator.serviceWorker.register(path).then(PushMonkeyWP.initialiseState);
};

PushMonkeyWP.showSubcriptionBanner = function() {

    if (PushMonkeyWP.getCookie('push_monkey_banner_dismissed')) {

      return
    }
    var text = '';
    if (push_monkey_locals.banner_position == "top" || 
      push_monkey_locals.banner_position == "bottom") {

      text += "<img src='" + push_monkey_locals.banner_icon_url + "' />";
      text += push_monkey_locals.banner_text;
      text += ' <button class="pm-subscribe-button" disabled></button>';
    } else {

      text += push_monkey_locals.banner_text;
      text += '<div class="button-container"><button class="pm-subscribe-button" disabled></button></div>';
    }
    var openAnimation = 'animated fadeInDown';
    var closeAnimation = 'animated fadeOutUp';
    var type = "success";
    var n = noty({

        text        : text,
        template: '<div class="noty_message"><div class="noty_text"></div><div class="noty_close"></div></div>',
        type        : type,
        dismissQueue: true,
        layout      : push_monkey_locals.banner_position,
        theme       : 'push-monkey-theme',
        maxVisible  : 10,
        closeWith   : ['button'],
        animation   : {

            open  : openAnimation,
            close : closeAnimation,
            easing: 'swing',
            speed : 1000
        },
        callback    : {

            onShow: function() {

                jQuery('.noty_container_type_success').css({'background-color': push_monkey_locals.banner_color});
                jQuery('.pm-subscribe-button').css({'background-color': push_monkey_locals.banner_subscribe_color});
            },
            onClose: function() {

              var counter_cookie = PushMonkeyWP.getCookie('push_monkey_banner_counter');
              if (!counter_cookie) {

                PushMonkeyWP.setCookie('push_monkey_banner_counter', 1, 365);                 
              } else {

                if (counter_cookie == "1") {

                  PushMonkeyWP.setCookie('push_monkey_banner_counter', 2, 365);
                } else {

                  PushMonkeyWP.setCookie('push_monkey_banner_dismissed', 1, 365);
                }
              }
            }
        }
    });
}

PushMonkeyWP.launch = function(){

  if (!push_monkey_locals.account_key) {

    return;
  }
	if (window.safari) {

		permission = window.safari.pushNotification.permission(PushMonkeyWPConfig.websiteID).permission;
		if(permission == 'default') {

			this.register();
		} else {

			PushMonkeyWPLog.log( 'Already registered or rejected.' );
		}
	} else if (this.hasServiceWorkers()) {

      this.activateSubscription()
  } else {

    PushMonkeyWPLog.log('No service workers available.');
  }
}

PushMonkeyWP.setCookie = function(name, value, days) {

    var expires;
    if (days) {

        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {

        expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

PushMonkeyWP.getCookie = function(c_name) {

    if (document.cookie.length > 0) {

        c_start = document.cookie.indexOf(c_name + "=");
        if (c_start != -1) {

            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";", c_start);
            if (c_end == -1) {

                c_end = document.cookie.length;
            }
            return unescape(document.cookie.substring(c_start, c_end));
        }
    }
    return false;
}

jQuery(document).ready(function($) {

  PushMonkeyWP.launch();
});
