				<div class="block well row-fluid">
					<div class="navbar">
						<div class="navbar-inner">
							<h5><?php echo $movie['title']; ?></h5>
						</div>
					</div>
					<div class="body">
					
					<p id="rightFloat" style="float: right;"><a href="/search/all/?q=<?php echo rawurlencode($original_term); ?>">Not the "<?php echo $original_term; ?>" you were looking for?</a></p>
					
					<div id="leftColumn" style="float: left;">
					<img id="mainImage" style="max-width: 300px;" src="<?php echo "data:image/jpg;base64,".base64_encode(file_get_contents($movie['poster_large'])); ?>">
					<hr>
					<center><h5>Actions</h5>
					<p><button onclick="checkmovie('<?php echo $movie['title']; ?>',1,this)" style="width: 180px;" class="btn btn-success">Check (HD)</button></p>
					<p><button onclick="checkmovie('<?php echo $movie['title']; ?>',0,this)" style="width: 180px;" class="btn btn-warning">Check (Standard)</button></p>
					<p><button onclick="trailer('<?php echo $movie['trailer']; ?>')" style="width: 180px;" class="btn btn-info">Watch Trailer</button></p></center>
					
					</div>
					
					<div id="rightColumn" style="margin-left: 320px;">
					<center>
					<ul class="midnav midnav-font no-background">
                        <li><a><i class="font-calendar"></i><span><?php echo $movie['release_date']; ?></span></a></li>
						<li><a><i class="font-time"></i><span><?php echo $movie['runtime']; ?> mins</span></a></li>
						<li><a><i class="font-globe"></i><span><?php echo $movie['genres'][0]; ?></span></a></li>
						
                    </ul>
					</center>
					<hr>
					<h5>Plot</h5>
					<p><?php echo $movie['plot']; ?></p>
					<hr>
					<h5>Directors</h5>
					<p><?php echo implode(", ",array_values($movie['directors'])); ?></p>
					<hr>
					<h5>Writers</h5>
					<p><?php echo implode(", ",array_values($movie['writers'])); ?></p>
					<hr>
					<h5>Cast</h5>
					<p><?php echo implode(", ",array_values($movie['cast'])); ?></p>
					<hr>
					<h5>Similar Titles</h5>
					<p>
					<?php
						$recommended = array_values($movie['recommended_titles']);
						$flag = false;
						foreach ($recommended as $item) {
							if ($flag)
								echo ', ';
							echo '<a href="/search/movie/?q='.rawurlencode($item).'">'.$item.'</a>';
							$flag = true;
						}
					?>
					</p>
					</div>
					
					<br style="clear: both;" />
					
					</div>
				</div>
