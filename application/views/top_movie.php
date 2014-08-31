
                    	<!-- Line chart -->
    					<div class="block well">
                            <div class="navbar">
                            	<div class="navbar-inner">
                                    <h5>Popular Movies</h5>
                                </div>
                            </div>
                            <div class="body">
								<ul class="thumbnails gallery">
								<?php
									foreach ($movies as $movie) {
										echo '<li><a alt="'.$movie['title'].'" href="/search/movie/?q='.rawurlencode($movie['title']).'" class="thumbnail"><img style="width: 120px; height: auto;" src="'.$movie['newposter'].'" /><center style="max-width: 120px; word-wrap: break-word; white-space: nowrap; overflow: hidden;">'.$movie['title'].'</center></a></li>';
									}
								?>
								</ul>
                            </div>
                        </div>
                        <!-- /line chart -->
