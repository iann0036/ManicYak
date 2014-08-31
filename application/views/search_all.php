				<div class="block well row-fluid">
					<div class="navbar">
						<div class="navbar-inner">
							<h5>Search Results for <?php echo $original_term; ?></h5>
						</div>
					</div>
					<div class="body">
					<?php
						foreach ($results as $result) {
					?>
						<div class="media">
							<a style="width: 64px;" class="pull-left" href="/search/<?php echo $result['shorttype'].'/?q='.rawurlencode($result['name']).'&uniqid='.$result['uniqid']; ?>"><img class="media-object" style="margin-left: auto; margin-right: auto; max-width: 64px; max-height: 64px;" src="<?php echo $result['image']; ?>" alt="<?php echo $result['name']; ?>" /></a>
							<div class="media-body">
								<h4 class="media-heading"><a href="/search/<?php echo $result['shorttype'].'/?q='.rawurlencode($result['name']).'&uniqid='.$result['uniqid']; ?>"><?php echo $result['name']." (".$result['type'].")"; ?></a></h4>
								<?php echo $result['summary']; ?><br />
								<i>Relevance: <?php echo $result['rating']; ?></i>
							</div>
						</div>
					<?php
						}
					?>
					</div>
				</div>
