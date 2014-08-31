				<div class="block well row-fluid">
					<div class="navbar">
						<div class="navbar-inner">
							<h5><?php echo $tv->Series->SeriesName; ?></h5>
						</div>
					</div>
					<div class="body">
					
					<p id="rightFloat" style="float: right;"><a href="/search/all/?q=<?php echo rawurlencode($original_term); ?>">Not the "<?php echo $original_term; ?>" you were looking for?</a></p>
					
					<div id="leftColumn" style="float: left;">
					<img id="mainImage" style="max-width: 300px;" src="<?php echo "data:image/jpg;base64,".base64_encode(file_get_contents('http://thetvdb.com/banners/'.$tv->Series->poster)); ?>">
					<hr>
					<center><h5>Actions</h5>
					<p>
					<?php
						if ($subscribed)
							echo '<button id="subscribeButton" onclick="unsubscribe(\''.rawurlencode($tv->Series->SeriesName).'\')" style="width: 180px;" class="btn btn-success">Subscribed</button>';
						else
							echo '<button id="subscribeButton" onclick="subscribe(\''.rawurlencode($tv->Series->SeriesName).'\')" style="width: 180px;" class="btn btn-primary">Subscribe</button>';
					?>
					</p></center>
					
					</div>
					
					<div id="rightColumn" style="margin-left: 320px;">
					<center>
					<ul class="midnav midnav-font no-background">
                        <li><a><i class="font-rss"></i><span><?php echo $tv->Series->Network; ?></span></a></li>
						<li><a><i class="font-cogs"></i><span><?php echo $tv->Series->Status; ?></span></a></li>
						<li><a><i class="font-globe"></i><span><?php $genres = explode("|",$tv->Series->Genre); echo $genres[1]; ?></span></a></li>
						
                    </ul>
					</center>
					<hr>
					<h5>Overview</h5>
					<p><?php echo $tv->Series->Overview; ?></p>
					<hr>
					<h5>Statistics</h5>
					Not available
					</div>
					
					<br style="clear: both;" />
					<hr>
					<h5>Episodes</h5>
					<div class="semi-block">
						<div class="tabbable tabs-right">
							<ul class="nav nav-tabs">
								<li class="active"><a href="#tab1" data-toggle="tab">Season 1</a></li>
								<?php
								$seasons = array_keys($episodes);
								foreach ($seasons as $season) {
									if ($season!=1)
										echo '<li><a href="#tab'.$season.'" data-toggle="tab">Season '.$season.'</a></li>';
								}
								?>
							</ul>
							<div class="tab-content">
								<?php
								foreach ($seasons as $season) {
									echo '<div class="tab-pane';
									if ($season==1)
									    echo ' active';
									echo '" id="tab'.$season.'">';
									foreach ($episodes[$season] as $episode) {
										echo '<h6>Episode '.(int)$episode['Combined_episodenumber'].' - '.$episode['EpisodeName'].'</h6>';
										if (is_string($episode['Overview']))
											echo '<p>'.$episode['Overview'].'</p>';
										else
											echo '<p>Description not available.</p>';
										echo '<p><button onclick="checktv(\''.str_replace("+","%20",urlencode($tv->Series->SeriesName)).'\','.$season.','.(int)$episode['Combined_episodenumber'].',this)" class="btn btn-mini">Check</button><br />&nbsp;</p>';
									}
									echo '</div>';
								}
								?>
							</div>
						</div>
					</div>
					
					</div>
				</div>
