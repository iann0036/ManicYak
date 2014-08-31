				<div class="block well row-fluid">
					<div class="navbar">
						<div class="navbar-inner">
							<h5><?php echo $music['artist']['name']; ?></h5>
						</div>
					</div>
					<div class="body">
					
					<p id="rightFloat" style="float: right;"><a href="/search/all/?q=<?php echo rawurlencode($original_term); ?>">Not the "<?php echo $original_term; ?>" you were looking for?</a></p>
					
					<div id="leftColumn" style="float: left; width: 300px;">
					<img id="mainImage" style="max-width: 300px;" src="<?php echo "data:image/png;base64,".base64_encode(file_get_contents($music['artist']['image'][4]['#text'])); ?>">
					
					</div>
					
					<div id="rightColumn" style="margin-left: 320px;">
					<?php
						if (isset($albums['topalbums']['album'])) {
					?>
						<h5>Albums</h5>
						<!--<?php print_r($albums); ?>-->
						<ul class="thumbnails gallery">
						<?php
							if (isset($albums['topalbums']['album']['name'])) {
								$album = $albums['topalbums']['album'];
								echo '<li><a alt="'.$album['name'].'" href="/album/?artist='.rawurlencode($music['artist']['name']).'&album='.rawurlencode($album['name']).'" class="thumbnail"><img style="max-width: 200px; max-height: 200px;" src="'.$album['image'][3]['#text'].'" alt=""><center style="max-width: 200px; word-wrap: break-word; white-space: nowrap; overflow: hidden;">'.$album['name'].'</center></a></li>';
							} else {
								foreach ($albums['topalbums']['album'] as $album) {
									echo '<li><a alt="'.$album['name'].'" href="/album/?artist='.rawurlencode($music['artist']['name']).'&album='.rawurlencode($album['name']).'" class="thumbnail"><img style="max-width: 200px; max-height: 200px;" src="'.$album['image'][3]['#text'].'" alt=""><center style="max-width: 200px; word-wrap: break-word; white-space: nowrap; overflow: hidden;">'.$album['name'].'</center></a></li>';
								}
							}
						?>
						</ul>
						<hr>
					<?php
						}
					?>
					<h5>Bio</h5>
					<p><?php echo str_replace("User-contributed text is available under the Creative Commons By-SA License and may also be available under the GNU FDL.","",$music['artist']['bio']['content']); ?></p>
					<?php
						if (isset($similar_artists)) {
					?>
						<hr>
						<h5>Similar Artists</h5>
						<p><?php echo $similar_artists; ?></p>
					<?php
						}
					?>
					</div>
					
					<br style="clear: both;" />
					
					</div>
				</div>
