				<form action="/profile/" method="post" enctype="multipart/form-data" class="form-horizontal">
					<fieldset>
    					<div class="block well row-fluid">
                            <div class="navbar">
                            	<div class="navbar-inner">
                                    <h5>Profile</h5>
                                </div>
                            </div>
							
							<div class="control-group">
								<label class="control-label">Usage:</label>
								<div class="controls">
								<?php $percentage = min(100,floor($downloads/(5*1024)*100)); ?>
								<div class="progress progress-<?php
								if ($percentage<75)
									echo "success";
								else if ($percentage<100)
									echo "warning";
								else
									echo "danger";
								?> value"><div class="bar filled-text" data-percentage="<?php echo $percentage; ?>"></div></div><br />
								<center><b>You have used <?php echo number_format(($downloads/1024),2); ?> GB of your 5 GB allowance</b></center></div>
							</div>
							<?php if ($this->session->userdata('type')=='internal') { ?>
							<div class="control-group">
								<label class="control-label">Username:</label>
								<div class="controls"><input disabled class="span12" type="text" name="username" value="<?php echo $user['username']; ?>" /></div>
							</div>
							
							<div class="control-group">
								<label class="control-label">Password:</label>
								<div class="controls"><input class="span12" type="password" name="password" /></div>
							</div>
							
							<div class="control-group">
								<label class="control-label">Real Name:</label>
								<div class="controls"><input class="span12" type="text" name="realname" value="<?php echo $user['realname']; ?>" /></div>
							</div>
							
							<div class="control-group">
								<label class="control-label">E-mail:</label>
								<div class="controls"><input class="span12" type="text" name="email" value="<?php echo $user['email']; ?>" /></div>
							</div>
							
							<div class="control-group">
								<label class="control-label">Photo:</label>
								<div class="controls"><input type="file" class="style" name="userfile" /></div>
							</div>
							
							<div class="form-actions align-right">
								<button type="submit" class="btn btn-primary">Update Profile</button>
							</div>
							<?php } ?>
                        </div>
                        
					</fieldset>
				</form>
