					<div class="row-fluid">
                      <div class="span8">
    					<div class="block well">
                            <div class="navbar">
                            	<div class="navbar-inner">
                                    <h5>Welcome</h5>
                                </div>
                            </div>
                            <div class="body">
                                <center>
									<h2>Welcome to Manic Yak</h2>
									<h5>Enter a search term below to start</h5>
									<br />
									<p>
									<form action="/search/" method="post" class="search-block">
										<input type="text" name="q" placeholder="Search..." autocomplete="off">
										<button type="submit" value="" class="btn"><span class="search"></span></button>
									</form>
									</p>
									<br />
									<hr>
									<h4>About Manic Yak</h4>
									<p>Manic Yak is a service that provides you with your favorite movies, music albums and TV show episodes quickly and painlessly. Full metadata support, with help from integrations such as <a href="http://www.imdb.com/" target="_blank">IMDb</a>, <a href="http://www.rottentomatoes.com/" target="_blank">RottenTomatoes</a>, <a href="http://www.last.fm/" target="_blank">Last.FM</a>, and <a href="http://www.thetvdb.com/" target="_blank">TheTVDB</a> allows you to know gain useful insights into your media. Manic Yak was developed by <a href="http://ian.mn" target="_blank">Ian Mckay</a> with the original purpose to help people avoid the overheads that traditional media gathering methods incur. For legal or DMCA issues please <a href="mailto:admin@manicyak.com">e-mail me</a>. We fully comply with all valid DMCA notices issued as per our <a href="/tos/" target="_blank">terms of service</a>.</p>
								</center>
                            </div>
                        </div>
					  </div>
					  <div class="span4">
						<div class="block well">
                        	<div class="navbar">
                            	<div class="navbar-inner">
                                	<h5>Subscribed Items</h5>
                                </div>
                            </div>
                            <div class="table-overflow">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">Name</th>
                                        </tr>
                                    </thead>
									<tbody>
									<?php
										if (count($subscriptions)==0)
											echo '<tr><td><center><b>No subscribed items found</b></center></td></tr>';
										foreach ($subscriptions as $subscription)
											echo '<tr><td><center><a href="/search/tv/?q='.rawurlencode($subscription->media_name).'"><b>'.$subscription->media_name.'</b></a></center></td></tr>';
									?>
									</tbody>
                                </table>
                            </div>
                        </div>
					  </div>
					</div>