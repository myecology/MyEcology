<?php
/* @var $this yii\web\View */

$this->title = Yii::$app->name;
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<div class="site-index">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $member['total'] ?></h3>
                    <p>会员总数</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?= $member['today'] ?></h3>
              <p>今日新增</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-plus"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>10</h3>
              <p>日活跃数</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3>65</h3>
              <p>今日红包数</p>
            </div>
            <div class="icon">
              <i class="fa fa-bitcoin"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
		</div>
		
		<div class="col-md-8">
			<div class="box box-info">
            	<div class="box-header with-border">
              		<h3 class="box-title">Latest Orders</h3>
              		<div class="box-tools pull-right">
                		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                		</button>
                		<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              		</div>
            	</div>
            	<div class="box-body">
					<div class="table-responsive">
						<table class="table no-margin">
							<thead>
								<tr>
									<th>Order ID</th>
									<th>Item</th>
									<th>Status</th>
									<th>Popularity</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><a href="pages/examples/invoice.html">OR9842</a></td>
									<td>Call of Duty IV</td>
									<td><span class="label label-success">Shipped</span></td>
									<td>
									<div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
									</td>
								</tr>
								<tr>
									<td><a href="pages/examples/invoice.html">OR1848</a></td>
									<td>Samsung Smart TV</td>
									<td><span class="label label-warning">Pending</span></td>
									<td>
									<div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
									</td>
								</tr>
								<tr>
									<td><a href="pages/examples/invoice.html">OR7429</a></td>
									<td>iPhone 6 Plus</td>
									<td><span class="label label-danger">Delivered</span></td>
									<td>
									<div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
									</td>
								</tr>
								<tr>
									<td><a href="pages/examples/invoice.html">OR7429</a></td>
									<td>Samsung Smart TV</td>
									<td><span class="label label-info">Processing</span></td>
									<td>
									<div class="sparkbar" data-color="#00c0ef" data-height="20">90,80,-90,70,-61,83,63</div>
									</td>
								</tr>
								<tr>
									<td><a href="pages/examples/invoice.html">OR1848</a></td>
									<td>Samsung Smart TV</td>
									<td><span class="label label-warning">Pending</span></td>
									<td>
									<div class="sparkbar" data-color="#f39c12" data-height="20">90,80,-90,70,61,-83,68</div>
									</td>
								</tr>
								<tr>
									<td><a href="pages/examples/invoice.html">OR7429</a></td>
									<td>iPhone 6 Plus</td>
									<td><span class="label label-danger">Delivered</span></td>
									<td>
									<div class="sparkbar" data-color="#f56954" data-height="20">90,-80,90,70,-61,83,63</div>
									</td>
								</tr>
								<tr>
									<td><a href="pages/examples/invoice.html">OR9842</a></td>
									<td>Call of Duty IV</td>
									<td><span class="label label-success">Shipped</span></td>
									<td>
									<div class="sparkbar" data-color="#00a65a" data-height="20">90,80,90,-70,61,-83,63</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="box-footer">
              		<a href="javascript:void(0)" class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
              		<a href="javascript:void(0)" class="btn btn-sm btn-default btn-flat pull-right">View All Orders</a>
            	</div>
            </div>
        </div>
		<div class="col-md-4">
			<div class="box box-primary">
            	<div class="box-header with-border">
              		<h3 class="box-title">Recently Added Products</h3>
              		<div class="box-tools pull-right">
                		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                		<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              		</div>
            	</div>
            	<div class="box-body">
              		<ul class="products-list product-list-in-box">
                		<li class="item">
                  			<div class="product-img">
                    			<img src="<?= $directoryAsset ?>/img/default-50x50.gif" alt="Product Image">
                  			</div>
                  			<div class="product-info">
                    			<a href="javascript:void(0)" class="product-title">Samsung TV
                      				<span class="label label-warning pull-right">$1800</span></a>
                    				<span class="product-description">
                          				Samsung 32" 1080p 60Hz LED Smart HDTV.
                        			</span>
                  			</div>
                		</li>
                		<li class="item">
                  			<div class="product-img">
                    			<img src="<?= $directoryAsset ?>/img/default-50x50.gif" alt="Product Image">
                  			</div>
                  			<div class="product-info">
                    			<a href="javascript:void(0)" class="product-title">Bicycle
                      				<span class="label label-info pull-right">$700</span></a>
                    				<span class="product-description">
                          				26" Mongoose Dolomite Men's 7-speed, Navy Blue.
                        			</span>
                  			</div>
                		</li>
                		<li class="item">
                  			<div class="product-img">
                    			<img src="<?= $directoryAsset ?>/img/default-50x50.gif" alt="Product Image">
                  			</div>
                  			<div class="product-info">
                    			<a href="javascript:void(0)" class="product-title">Xbox One 
									<span class="label label-danger pull-right">$350</span></a>
                    				<span class="product-description">
                          				Xbox One Console Bundle with Halo Master Chief Collection.
                        			</span>
                  			</div>
                		</li>
                		<li class="item">
                  			<div class="product-img">
                    			<img src="<?= $directoryAsset ?>/img/default-50x50.gif" alt="Product Image">
                  			</div>
                  			<div class="product-info">
                    			<a href="javascript:void(0)" class="product-title">PlayStation 4
                      				<span class="label label-success pull-right">$399</span></a>
                    				<span class="product-description">
                          				PlayStation 4 500GB Console (PS4)
                        			</span>
                  			</div>
						</li>
              		</ul>
            	</div>
            	<div class="box-footer text-center">
              		<a href="javascript:void(0)" class="uppercase">View All Products</a>
            	</div>
          	</div>
		</div>
	</div>
	

		
</div>
