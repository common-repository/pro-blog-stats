<div class="wrap">
    <style type="text/css">
//div
//{
//font-family:Verdana, Arial, Helvetica, sans-serif, Amelia, "NewsGoth BT";
//font-size:13px;
//line-height:19px;
//background:#f9f9f9;
//}
.pbs_twitter { border:1px solid #ccc; }
h1
{
font-family:Georgia, "Times New Roman", Times, serif;
font-style:italic;
color:#4c4c4cl;
font-size:30px;
margin:23px 0px;
padding:0px;
font-weight:normal;
}
h2
{
color:#268ab4 !important;
margin:0px 0px 8px 0px;
padding:0px;
font-weight:normal !important;
font-style:normal !important;
}
p
{
font-size:13px;
margin:0px 0px 8px 0px;
line-height:20px;
}
.link_color{
color:#3366FF;
}
.details_page img{
	display:block;
	margin:10px 0;
}
.details_page h2{
	border-bottom:1px solid ;
	margin:0 0 10px; padding:0 15px 3px 0;
	}

</style>
<div class="details_page">
        <h1>Pro Blog Stats Help</h1>
 <h2 id="info">For your information</h2>

        <p>If you are running the report page at the first time, please note that it can take up to nearly 20 minutes to gather the full amount of data . Also note that you need to set up at least one of the services except google analytics for gather data from report page.
        </p>

        <h2 id="feedburner">Feedburner</h2>

        <p>Please enter you Feedburner account name.<br />
        The Feedburner Awareness API must be turned on in your Feedburner account. This is located under Publicize > Awareness API.<br />
        Take your Feedburner feed domain and only enter the account name.<br />
        For instance, if your feed domain is:<br />
        <span class="link_color">http://feeds.feedburner.com/blogtechguy</span><br />
        The account name for this feed is &quot; blogtechguy&quot; ... so enter &quot;blogtechguy&quot;.
        </p>
        <img src="<?php echo $this->bprpPluginPath().'media/feedburner_name.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" />


        <h2 id="twitter">Twitter</h2>

        <p>Enter your Twitter username. The Twitter username you enter is without the <span class="link_color">'@'</span>.<br />
        For example, if your Twitter account is<span class="link_color"> www.Twitter.com/billyboy</span> you would just enter 'billyboy' as your Twitter username.
        </p>
        <img src="<?php echo $this->bprpPluginPath().'media/twitter.jpg'; ?>" alt="FeedBurner Name"  class="tooltip pbs_twitter" />


         <h2 id="facebook">Facebook</h2>

        <p>You need to enter your Facebook  Page ID.<br />
        To find your Facebook page ID:<br />
1. Go to your Facebook page<br />
2. Click on your Facebook Page profile image
        </p>
        <img src="<?php echo $this->bprpPluginPath().'media/facebook_1.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" />
        <p>3. After clicking on the image, the domain url will change to include your Facebook Page ID.</p>
        <img src="<?php echo $this->bprpPluginPath().'media/facebook_2.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" />
        So in this case, the Facebook page id is: "111237306582"  and that is the code you enter.
 <img src="<?php echo $this->bprpPluginPath().'media/facebook_3.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" />

  <h2 id="digg">Digg</h2>

    <p>Enter your Digg username.<br />
    For instance, if your Digg profile domain is:<br />
<span class="link_color">http://digg.com/users/andrewrondeau</span><br />
The account name for this is 'andrewrondeau' ...so enter 'andrewrondeau'.</p>

    <img src="<?php echo $this->bprpPluginPath().'media/digg.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" />


     <h2 id="postrank">PostRank Feed Hash</h2>

    <p>Download, install and activate the PostRank plugin from here:<br />
<span class="link_color">http://www.postrank.com/publishers/wordpress</span><br />

Once activated, click on the <span class="link_color">'PostRank'</span> link under<span class="link_color"> 'Settings'</span> from within your WordPress Dashboard.</p>

    <img src="<?php echo $this->bprpPluginPath().'media/postrank2.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />
Add the code you have copied into the Pro Blog Stats set-up:

    <img src="<?php echo $this->bprpPluginPath().'media/postrank3.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />
    You can then deactivate the PostRank plugin.If you have just joined PostRank you will have to wait a couple of days before they provide you with your PostRank Feed Hash.



     <h2 id="clcky">Clicky</h2>

    <p>To see your Clicky stats:<br />
1. Register an account at Clicky:<span class="link_color"> http://getclicky.com</span><br />

2.Once registered, install the Clicky WordPress plugin on your blog, from this page:<br />
<span class="link_color">http://getclicky.com/goodies/#wordpress</span><br />
3. From your WordPress Dashboard, click the link 'Clicky' under 'Settings' on the left hand side.<br />


4.From the Clicky settings page, click the link, 'user homepage on Clicky'</p>

    <img src="<?php echo $this->bprpPluginPath().'media/click.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />
    5. From the Clicky user home page, click click "Preferences" under the name of the domain.
<img src="<?php echo $this->bprpPluginPath().'media/click2.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />
<span id="cliky_key">6. Here, you will find the Site ID, Site Key, Admin Site Key and Database Server under Site information.</span>
<img src="<?php echo $this->bprpPluginPath().'media/click3.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />
7.Copy the 'Clicky Site Id' and 'Clicky Site Key' into the Pro Blog Stats set up page.
<img src="<?php echo $this->bprpPluginPath().'media/click4.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />


  <h2 id="wordpress_api">WordPress.com API</h2>

   <p>
   To obtain your WordPress.com API key, you need to register for a WordPress.com account at <span class="link_color"http://www.wordpress.com/signup></span><br />
<br />
After registering, to find your key, access your <span class="link_color">WordPress.com</span> Personal Settings at <span class="link_color">http://dashboard.wordpress.com/wp-admin/users.php?page=grofiles-user-settings </span> - you should see, directly under the Personal Settings heading, a sentence which explains, <span class="link_color">'Your WordPress.com API key is:'</span> followed by a string of 12 letters and numbers.<br />
Copy that code into the WordPress.com API field.
   </p>

 <h2 id="klout">Klout</h2>

   <p>
 To see you Klout stats...<br />
1. Sign up by going here: <span class="link_color"> http://klout.com/</span> </<br />
You can sign up with your Twitter account.<br />

Your  <span class="link_color">Klout username</span> is the same as your  <span class="link_color">Twitter username</span>. For instance,  <span class="link_color">andrewrondeau</span>
   </p>
<img src="<?php echo $this->bprpPluginPath().'media/klout.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />


<h2 id="other_stat">Other Statistics</h2>
<p>Simply check the boxes of the statistics you'd like to see.</p>
<img src="<?php echo $this->bprpPluginPath().'media/other.jpg'; ?>" alt="FeedBurner Name"  class="tooltip" align="middle" />
<p>Make sure you click the <span class="link_color">'Save'</span> button.</p>

<h2 id="google_analytics">Google Analytics</h2>
<p>Click the <span class="link_color">'Add'</span> button and follow the instructions to get an authentication token from Google.<br />
Once you have gotten the authentication token you will have the facility to use the drop down menu to choose which web domain you wish to receive Google Analytics on.<br />
Click the <span class="link_color">'Save'</span> button</p>
</div>

<?php /* DISABLE AWEBER HELP
<h2 id="aweber">Aweber</h2>
<p>Click the <span class="link_color">'Get authorization code'</span> button and follow the instructions to get an authentication code from Aweber.<br />
Once you have gotten the authentication code, copy and paste it in the text field.<br />
Click the <span class="link_color">'Save'</span> button</p>
*/ ?>
</div>
