<?php
$whr = (!empty($_POST['InboxKey']) ? "and (`from` like '%$_POST[InboxKey]%' or message like '%$_POST[InboxKey]%')" : "");
$s = "Select * from chat where `to` like '$_SESSION[username]' $whr order by sent DESC limit 30";
$r = _query($s);
$s1 = "Select * from chat where `from`='$_SESSION[username]' group by `to` order by sent DESC limit 30";
$r1 = _query($s1);
//while 
?>
<div class="row">
                    <div class="col-lg-12">
                        <div class="portlet portlet-default">
                            <div class="portlet-body">

                                <nav class="navbar mailbox-topnav" role="navigation">
                                    <!-- Brand and toggle get grouped for better mobile display -->
                                    <div class="navbar-header">
                                        <a class="navbar-brand" href="#"><i class="fa fa-inbox"></i> Inbox</a>
                                    </div>

                                    <!-- Collect the nav links, forms, and other content for toggling -->
                                    <div class="mailbox-nav">
                                        <form class="navbar-form navbar-right visible-lg" action="?" method="post" role="search">
                                            <div class="form-group">
                                                <input type="text" name="InboxKey" value="<?php echo $_POST['InboxKey']?>" class="form-control" placeholder="Cari Pesan...">
                                            </div>
                                            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i>
                                            </button>
                                        </form>
                                    </div>
                                </nav>

                                <div id="mailbox">

                                    <ul class="nav nav-pills nav-stacked mailbox-sidenav">
                                        <li class="nav-divider"></li>
                                        <li class="mailbox-menu-title text-muted">Folder</li>
                                        <li class="active"><a href="?mnux=pesan/inbox">Inbox (<?php echo _num_rows($r);?>)</a>
                                        </li>
                                        <li><a href="?mnux=pesan/outbox">Sent (<?php echo _num_rows($r1);?>)</a>
                                        </li>
                                    </ul>

                                    <div id="mailbox-wrapper">

                                        <div class="table-responsive mailbox-messages">
                                            <table class="table table-bordered table-striped table-hover">
                                                <tbody>
                                                <?php while($w = _fetch_array($r)){ ?>
                                              <tr class="<?php echo ($w['recd']==0? "unread-message":"")?> clickableRow" onclick="onclick=chatWith('<?php echo $w['from']?>')">
                                                        <td class="checkbox-col">
                                                            <input type="checkbox" class="selectedId" name="selectedId">
                                                        </td>
                                                        <td class="from-col"><?php echo $w['from']?></td>
                                                        <td class="msg-col">
                                                            <span class="text-muted"><?php echo $w['message']?></span>
                                                        </td>
                                                        <td class="date-col"><small><?php echo $w['sent']?></small></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>