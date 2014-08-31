				<form action="/settings/" method="post" class="form-horizontal">
					<fieldset>
    					<div class="block well row-fluid">
                            <div class="navbar">
                            	<div class="navbar-inner">
                                    <h5>Settings</h5>
                                </div>
                            </div>
							
							<div class="control-group">
								<label class="control-label">Use iTunes to listen:</label>
								<div class="controls on_off">
									<div class="checkbox inline"><input type="checkbox"<?php if ($pref_itunes) echo ' checked="checked"'; ?> name="pref_itunes" /></div>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label">Automatically gather subscribed shows:</label>
								<div class="controls on_off">
									<div class="checkbox inline"><input type="checkbox"<?php if ($pref_autogather) echo ' checked="checked"'; ?> name="pref_autogather" /></div>
								</div>
							</div>
							
							<div class="control-group">
								<label class="control-label">Media tracker:</label>
								<div class="controls on_off">
									<div class="checkbox inline"><input type="checkbox"<?php if ($pref_ack) echo ' checked="checked"'; ?> name="pref_ack" /></div>
								</div>
							</div>

                            <div class="control-group">
                                <label class="control-label">Google Drive&trade; integration:</label>
                                <div class="controls">
                                    <?php
                                    if ($google_openid) {
                                        echo '<img src="/images/drive_icon.png" style="width: 24px; height: 24px; position: absolute;" /><div style="margin-left: 28px;"><b>Connected</b></div>';
                                    } else {
                                        echo '<a href="/settings/drive/"><img src="/images/drive_icon.png" style="width: 24px; height: 24px; position: absolute;" /><div style="margin-left: 28px;"><b>Connect</b></a></div>';
                                    }
                                    ?>
                                </div>
                            </div>
							
							<input type="hidden" name="formpost" value="true" />
							<div class="form-actions align-right">
								<button type="submit" class="btn btn-primary">Update Settings</button>
							</div>
                        </div>
                        
					</fieldset>
				</form>
