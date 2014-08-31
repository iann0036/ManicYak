
                    	<!-- Line chart -->
    					<div class="block well">
                            <div class="navbar">
                            	<div class="navbar-inner">
                                    <h5>Popular TV</h5>
                                </div>
                            </div>
							<div class="body">
								<ul class="thumbnails gallery">
								<?php
									foreach ($tv as $item) {
										echo '<li><a alt="'.$item.'" href="/search/tv/?q='.rawurlencode($item).'" class="thumbnail"><img style="width: 150px; height: auto;" src="/images/tvposters/'.preg_replace("/[^a-zA-Z0-9]+/","",$item).'.jpg" /><center style="max-width: 150px; word-wrap: break-word; white-space: nowrap; overflow: hidden;">'.$item.'</center></a></li>';
									}
								?>
								</ul>
                            </div>
                        </div>
                        <!-- /line chart -->
