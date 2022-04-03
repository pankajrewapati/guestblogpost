=== Add Blog ===
Github = https://github.com/pankajrewapati/guestblogpost
https://www.loom.com/share/9423c2c72e094de7b77daa033d769836
= 1.2.4 =


1. login by admin and check setting page on plugin, right now we have not more setting so i just added 2 shortcode

2. Now going to logout and check view post page, here should show only publish post (should not show pending post), here we can seen show only view more button not approve and other button

3. Now going to login by author and go to add post page , which we showad by our shortcode ([BlogForm bgcolor="#cccccc"])

4. Add 2 new post and now go to view post page, , which we showad by our shortcode ([BlogShow bgcolor="#cccccc" post_per_page="10"]), but here not showing any publist post becasue our post need to approved

	- We can check inside textarea user can not insert HTML tag(This is simple html tag we can use backend as well) 

	- Add image will be add in post feature image and we can seen in wordpress backend list.	

pending = means un-approve
publish = means approved

5. Now going to login by admin and approved post from same list page.

6. Now will show approved(means publish) post in list.

7. we can also check here added pagination as well, this is very simple pagination using by JQ.


= I just use simple css , not use bootstrap and other (we can make good design using bootstrap)
We can also use 'register_activation_hook' and 'register_activation_hook' for default plugin setting and clean database value (related to this plugin) when deactive plugin
OR we can use uninstall.php fiel in root directory of plugin
this will load wehn we will deactive plugin.
