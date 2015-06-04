=== Subscribe to Comments Reloaded Better Unsubscribe ===
Contributors: FolioVision
Donate link: http://foliovision.com/donate
Tags: subscribe, comments, notification, subscription, manage, double check-in, follow, commenting, unsubscribe, quick, better
Requires at least: 3.6
Tested up to: 4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Unsubscribing from comment notifications is not quick and easy enough in Subscribe to Comments Reloaded. This addon plugin fixes that.

== Description ==

Subscribe to Comments Reloaded is a powerful plugin which allows users to subscribe to new comments on your blog posts. However if a person want to unsubscribe, they have to click the "Manage your subscriptions" link in the notification email. That opens a custom subscription management page on your blog, where they have to select the posts they wish to be unsubscribed from, then select the "Action" (Delete, Suspend, etc.) and then click "Update".

What should be quick and easy is a multi-step process.

This plugin unsubscribes the person right away from the post for which the notification was received and adds a "You are now unsubscribed from {post title}" visual notification about that to the above management page. That is a single click to unsubscribe. But they can still use the subscription management page to adjust other subscriptions.

**Update March 2015**: SendGrid bounce processing added! If you use WP Mail SMTP or Mailer plugins, we detect the SendGrid login and then check for bounces in a WP cron. Bounced emails get unsubscribed from comment notifications.

[Support](http://foliovision.com/support/subscribe-to-comments-reloaded-better-unsubscribe) |
[Change Log](http://foliovision.com/wordpress/plugins/subscribe-to-comments-reloaded-better-unsubscribe/changelog) |
[Installation](http://foliovision.com/wordpress/plugins/subscribe-to-comments-reloaded-better-unsubscribe/installation)|
[FAQ](http://foliovision.com/wordpress/plugins/subscribe-to-comments-reloaded-better-unsubscribe/faq)


== Installation ==

Just install the plugin using Wordpress admin dashboard, or upload the ZIP file.

We recommend that you test the plugin. Here are detailed steps:

* after Subscribe to Comments Reloaded was installed activate this plugin
* comment and subscribe to some post as a non-logged in visitor
* approve the comment or confirm the subscription if you are using double opt-in feature of Subscribe to Comments Reloaded
* post another comment using different email
* check that you received your notification
* check that the "Manage your subscriptions" was changed to "Unsubscribe" and that clicking the link opens the subscription management page with "You are now unsubscribed from {post title}" message at the top. You can see what it should look like on the screenshot on the plugin page.
* use [our support forums](http://foliovision.com/support/subscribe-to-comments-reloaded-better-unsubscribe) to report any issues
* if you want to use the **bounce processing**, you have to use **SendGrid** and either [WP Mail SMPT](https://wordpress.org/plugins/wp-mail-smtp/) or [Mailer](https://wordpress.org/plugins/mailer/) to send your Wordpress emails

== Frequently Asked Questions ==

= How can I check if this works properly? =

Please read the installation steps.

== Screenshots ==

1. Here's what the commenter's new comment notification should look like

2. Here's the instant unsubscribe in action

== Changelog ==

= 0.9.7 =

* Fix for Subscribe to Comments Reloaded 150422 - the email address is no longer in URL (thank god!), so we must decode the special parameter.

= 0.9.6 =

* Fix for Google Personally Identifiable Information warnings - Subscribe to Comments Reloaded shows the subscription manager in your theme, which is nice. However the URL contains the user email address and if your theme sidebar has Google ads in it, these ads can see the user email and thus persons identity is revealed. That's why Subscribe to Comments Relaoaded Better Unsubscribe loads a bare bones template for the subscription manager only. This is an important fix until Subscribe to Comments Reloaded gets fixed.

= 0.9.5 =

* New cron function which automatically unsubscribes all users (emails) which are bounced, invalid , spam or unsubscribed at Sendgrid.com. Read the installation guide.

= 0.9.4 =

* Bugfix for Subscribe to Comments Reloaded - since this plugin puts up wrong shortlink and canonical link for the management page, Subscribe to Comments Reloaded Better Unsubscribed removes these links from this virtual management page.

= 0.9.3 =

* Improvement - "This notification was sent to {email}." text added at the end of notification email. It's handy if you get spam reports for your comment notifications. Some mail servers hide the "from" address, so you don't know who reported your notification as a spam and you can't unsubscribe him.

= 0.9.2 =

* Bugfix - post ID was sometimes not properly obtained, resulting in no unsubscribe action
* Bugfix - the subscription is now only suspended, rather than being suspended and unconfirmed at the same time. That makes it easier to re-subscribe.

= 0.9.1 =

* Bugfix - better checking of what links in emails get adjusted

= 0.9 = 

* Initial release

== Upgrade Notice ==

= 0.9 = 

* Initial release
