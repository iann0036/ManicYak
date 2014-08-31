
                    	<!-- Line chart -->
    					<div class="block well">
                            <div class="navbar">
                            	<div class="navbar-inner">
                                    <h5>Popular Artists</h5>
                                </div>
                            </div>
                            <div class="body">
								<ul class="thumbnails gallery">
								<?php
									foreach ($artists as $artist) {
										echo '<li><a alt="'.$artist['name'].'" href="/search/music/?q='.rawurlencode($artist['name']).'" class="thumbnail"><img style="width: 150px; height: auto;" src="'.$artist['poster'].'" /><center style="max-width: 150px; word-wrap: break-word; white-space: nowrap; overflow: hidden;">'.$artist['name'].'</center></a></li>';
									}
								?>
								</ul>
                            </div>
                        </div>
                        <!-- /line chart -->
