
                    	<div class="block well">
                        	<div class="navbar">
                            	<div class="navbar-inner">
                                	<h5>Media</h5>
                                </div>
                            </div>
                            <div class="table-overflow">
                                <table class="table table-striped" id="data-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">Name</th>
                                            <th style="text-align: center; width: 150px;">Type</th>
											<th style="text-align: center; width: 150px;">Added</th>
											<th style="text-align: center; width: 150px;">Status</th>
                                            <th style="text-align: center; width: 300px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
										foreach ($media as $item) {
											if ($item->ack==1)
												$ack = 0;
											else
												$ack = 1;
										
											echo '<tr id="media-row-'.$item->id.'">
											<td style="text-align: center;">';
											if ($item->type=="movie")
												echo '<a href="/search/movie/?q='.rawurlencode($item->media_name).'">';
											else if ($item->type=="music")
												echo '<a href="/album/?artist='.rawurlencode($item->media_name).'&album='.rawurlencode($item->media_attr).'">';
											else if ($item->type=="tv")
												echo '<a href="/search/tv/?q='.rawurlencode($item->media_name).'">';
											
											echo $item->display_name;
											if ($item->type=="movie") {
												if ($item->media_attr==1)
													echo ' (HD)';
												else
													echo ' (Standard)';
											}
											echo '</a></td><td style="text-align: center;">';
											if ($item->type == "movie")
												echo '<span class="label label-warning">Movie</span>';
											else if ($item->type == "music")
												echo '<span class="label label-success">Music</span>';
											else
												echo '<span class="label">TV Show</span>';
											
											echo '<td style="text-align: center;"><b style="display: none;">'.$item->added.'</b><b>'.$item->readableTime.'</b></td>';
											
											if ($item->status=="complete") {
												echo '</td>
													<td style="text-align: center;"><b><span class="text-success">COMPLETE</span></b></td>
													<td style="text-align: right;">';
												if ($item->type == "music") {
													if (!$pref_itunes) {
														echo '<a ';
														if ($ack)
															echo 'onclick="doack('.$item->id.')"';
														echo ' style="color: #FFFFFF;" href="/download/playlist/'.$item->id.'"><button type="button" class="btn btn-mini btn-info"><b class="icon-play-circle"></b>Listen</button></a>';
													} else {
														echo '<a ';
														if ($ack)
															echo 'onclick="doack('.$item->id.')"';
														echo ' style="color: #FFFFFF;" href="itms://'.$_SERVER['SERVER_NAME'].'/playlists/'.$item->id.'/playlist.m3u"><button type="button" class="btn btn-mini btn-info"><b class="icon-play-circle"></b>Listen</button></a>';
													}
												} else {
													if ($item->streamable > 0)
														echo '<button onclick="watch('.$item->id.','.$ack.')" type="button" class="btn btn-mini btn-info"><b class="icon-film"></b>Watch</button>';
													else
														echo '<button disabled type="button" class="btn btn-mini"><b class="icon-film"></b>Watch</button>';
												}
												if ($this->session->userdata('pref_ack')>0) {
													echo '&nbsp;';
													if ($ack)
														echo '<button onclick="doack('.$item->id.')" id="ack-'.$item->id.'" type="button" class="btn btn-mini btn-info">&nbsp;<b class="icon-eye-close"></b></button>';
													else
														echo '<button onclick="undoack('.$item->id.')" id="ack-'.$item->id.'" type="button" class="btn btn-mini btn-info">&nbsp;<b class="icon-eye-open"></b></button>';
												}
												echo '&nbsp;<button onclick="download('.$item->id.','.$ack.')" type="button" class="btn btn-mini btn-info"><b class="icon-download-alt"></b>Download</button>&nbsp;<button onclick="doDelete('.$item->id.')" type="button" class="btn btn-mini btn-info"><b class="icon-remove"></b>Delete</button></td></tr>';
											} else {
												echo '</td>
													<td style="text-align: center;"><b><span class="text-warning">PARTIAL</span></b></td>
													<td style="text-align: center;">';
												if ($item->type == "music") {
													echo '<a style="color: #FFFFFF;" href="#"><button disabled type="button" class="btn btn-mini"><b class="icon-play-circle"></b>Listen</button></a>';
												} else {
													echo '<button disabled type="button" class="btn btn-mini"><b class="icon-film"></b>Watch</button>';
												}
												if ($this->session->userdata('pref_ack')>0) {
													echo '&nbsp;';
													if ($ack)
														echo '<button onclick="doack('.$item->id.')" id="ack-'.$item->id.'" type="button" class="btn btn-mini btn-info">&nbsp;<b class="icon-eye-close"></b></button>';
													else
														echo '<button onclick="undoack('.$item->id.')" id="ack-'.$item->id.'" type="button" class="btn btn-mini btn-info">&nbsp;<b class="icon-eye-open"></b></button>';
												}
												echo '&nbsp;<button disabled type="button" class="btn btn-mini"><b class="icon-download-alt"></b>Download</button>&nbsp;<button onclick="doDelete('.$item->id.')" type="button" class="btn btn-mini btn-info"><b class="icon-remove"></b>Delete</button></td></tr>';
											}
										}
									?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
						<style media="all" type="text/css">
							.center { text-align: center; }
						</style>
						<script>
						$(document).ready(function(){
							oTable = $('#data-table').dataTable({
								"bJQueryUI": false,
								"bRetrieve": true,
								"bDestroy": true,
								"bAutoWidth": false,
								"sPaginationType": "full_numbers",
								"sDom": '<"datatable-header">t<"datatable-footer"ip>',
								"oLanguage": {
									"sLengthMenu": "<span>Show entries:</span> _MENU_"
								}
							});
						});
						</script>
