				<div class="block well row-fluid">
					<div class="navbar">
						<div class="navbar-inner">
							<h5><?php echo $album['artist']." - ".$album['name']; ?></h5>
						</div>
					</div>
					<div class="body">
					<div id="leftColumn" style="float: left; width: 300px;">
					<img id="mainImage" style="max-width: 300px;" src="<?php echo "data:image/png;base64,".base64_encode(file_get_contents($album['image'][3]['#text'])); ?>">
					<hr>
					<center><h5>Actions</h5>
					<p><button id="downloadButton" onclick="checkmusic('<?php echo $album['artist'].'\',\''.$album['name']; ?>',this)" style="width: 180px;" class="btn btn-primary">Check</button></p></center>
					
					</div>
					
					<div id="rightColumn" style="margin-left: 320px;">
					<center>
					<ul class="midnav midnav-font no-background">
                        <li><a><i class="font-calendar"></i><span><?php echo $album['releasedate']; ?></span></a></li>
						<li><a><i class="font-time"></i><span><?php echo $total_duration.' minutes'; ?></span></a></li>
                    </ul>
					</center>
					<!--<?php print_r($album); ?>-->
					<hr>
					<h5>Tracks</h5>
					<?php
						foreach ($album['tracks']['track'] as $track) {
							echo '<p><b>'.$track['name'].' - '.floor($track['duration']/60).':'.str_pad($track['duration']%60,2,"0",STR_PAD_LEFT).'</b></p>';
						}
						
						if (isset($album['wiki']['summary'])) {
					?>
					<hr>
					<h5>Bio</h5>
					<p><?php echo $album['wiki']['summary']; ?></p>
					<?php
						}
					?>
					</div>
					
					<br style="clear: both;" />
					
					</div>
				</div>
