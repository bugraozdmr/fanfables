<?php include './components/up-all.php' ?>

<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Dashboard</h3>
        <h6 class="op-7 mb-2">Overview of sales and more</h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0">
        <a href="#" class="btn btn-label-info btn-round me-2">Manage Customers</a>
        <a href="./products.php" class="btn btn-primary btn-round">Add Product</a>
      </div>
    </div>
    <!-- 
<div class="row">
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-primary bubble-shadow-small">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Visitors</p>
                  <h4 class="card-title">1,294</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fas fa-user-check"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Cutomers</p>
                  <h4 class="card-title">1303</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fas fa-luggage-cart"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Sales</p>
                  <h4 class="card-title">₺ 1,345</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                  <i class="far fa-check-circle"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Order</p>
                  <h4 class="card-title">576</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>    
-->
    <div class="row row-card-no-pd">
      <div class="col-12 col-sm-6 col-md-6 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6><b>Todays Income</b></h6>
                <p class="text-muted">All Customs Value</p>
              </div>
              <h4 class="text-info fw-bold">$170</h4>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-info w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <p class="text-muted mb-0">Change</p>
              <p class="text-muted mb-0">75%</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-6 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6><b>Total Revenue</b></h6>
                <p class="text-muted">All Customs Value</p>
              </div>
              <h4 class="text-success fw-bold">$120</h4>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-success w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <p class="text-muted mb-0">Change</p>
              <p class="text-muted mb-0">25%</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-6 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6><b>New Orders</b></h6>
                <p class="text-muted">Fresh Order Amount</p>
              </div>
              <h4 class="text-danger fw-bold">15</h4>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-danger w-50" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <p class="text-muted mb-0">Change</p>
              <p class="text-muted mb-0">50%</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-6 col-xl-3">
        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h6><b>New Users</b></h6>
                <p class="text-muted">Joined New User</p>
              </div>
              <h4 class="text-secondary fw-bold">12</h4>
            </div>
            <div class="progress progress-sm">
              <div class="progress-bar bg-secondary w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <div class="d-flex justify-content-between mt-2">
              <p class="text-muted mb-0">Change</p>
              <p class="text-muted mb-0">25%</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">User Statistics</div>
              <div class="card-tools">
                <a href="#" class="btn btn-label-success btn-round btn-sm me-2">
                  <span class="btn-label">
                    <i class="fa fa-pencil"></i>
                  </span>
                  Export
                </a>
                <a href="#" class="btn btn-label-info btn-round btn-sm">
                  <span class="btn-label">
                    <i class="fa fa-print"></i>
                  </span>
                  Print
                </a>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="chart-container" style="min-height: 375px">
              <canvas id="statisticsChart"></canvas>
            </div>
            <div id="myChartLegend"></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card card-primary card-round">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Monthly Sales</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-sm btn-label-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Export
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Export Json</a>
                    <a class="dropdown-item" href="#">Export XML</a>
                    <a class="dropdown-item" href="#">Export TXT</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-category">March 25 - April 02</div>
          </div>
          <div class="card-body pb-0">
            <div class="mb-4 mt-2">
              <h1>$4,578.58</h1>
            </div>
            <div class="pull-in">
              <canvas id="dailySalesChart"></canvas>
            </div>
          </div>
        </div>
        <div class="card card-round">
          <div class="card-body pb-0">
            <div class="h1 fw-bold float-end text-primary">+5%</div>
            <h2 class="mb-2">17</h2>
            <p class="text-muted">Users online</p>
            <div class="pull-in sparkline-fix">
              <div id="lineChart"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card card-round">
          <div class="card-header">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">Transaction History</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <!-- Projects table -->
              <table class="table align-items-center mb-0">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Payment Number</th>
                    <th scope="col" class="text-end">Date & Time</th>
                    <th scope="col" class="text-end">Amount</th>
                    <th scope="col" class="text-end">Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row">
                      <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                        <i class="fa fa-check"></i>
                      </button>
                      Payment from #10231
                    </th>
                    <td class="text-end">Mar 19, 2020, 2.45pm</td>
                    <td class="text-end">$250.00</td>
                    <td class="text-end">
                      <span class="badge badge-success">Completed</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="card card-round">
          <div class="card-body">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">New Customers</div>
              <div class="card-tools">
                <div class="dropdown">
                  <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-list py-4">
              <div class="item-list">
                <div class="avatar">
                  <img src="assets/img/jm_denis.jpg" alt="..." class="avatar-img rounded-circle" />
                </div>
                <div class="info-user ms-3">
                  <div class="username">Jimmy Denis</div>
                  <div class="status">Graphic Designer</div>
                </div>
                <button class="btn btn-icon btn-link op-8 me-1">
                  <i class="far fa-envelope"></i>
                </button>
                <button class="btn btn-icon btn-link btn-danger op-8">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
              <div class="item-list">
                <div class="avatar">
                  <span class="avatar-title rounded-circle border border-white">CF</span>
                </div>
                <div class="info-user ms-3">
                  <div class="username">Chandra Felix</div>
                  <div class="status">Sales Promotion</div>
                </div>
                <button class="btn btn-icon btn-link op-8 me-1">
                  <i class="far fa-envelope"></i>
                </button>
                <button class="btn btn-icon btn-link btn-danger op-8">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
              <div class="item-list">
                <div class="avatar">
                  <img src="assets/img/talha.jpg" alt="..." class="avatar-img rounded-circle" />
                </div>
                <div class="info-user ms-3">
                  <div class="username">Talha</div>
                  <div class="status">Front End Designer</div>
                </div>
                <button class="btn btn-icon btn-link op-8 me-1">
                  <i class="far fa-envelope"></i>
                </button>
                <button class="btn btn-icon btn-link btn-danger op-8">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
              <div class="item-list">
                <div class="avatar">
                  <img src="assets/img/chadengle.jpg" alt="..." class="avatar-img rounded-circle" />
                </div>
                <div class="info-user ms-3">
                  <div class="username">Chad</div>
                  <div class="status">CEO Zeleaf</div>
                </div>
                <button class="btn btn-icon btn-link op-8 me-1">
                  <i class="far fa-envelope"></i>
                </button>
                <button class="btn btn-icon btn-link btn-danger op-8">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
              <div class="item-list">
                <div class="avatar">
                  <span class="avatar-title rounded-circle border border-white bg-primary">H</span>
                </div>
                <div class="info-user ms-3">
                  <div class="username">Hizrian</div>
                  <div class="status">Web Designer</div>
                </div>
                <button class="btn btn-icon btn-link op-8 me-1">
                  <i class="far fa-envelope"></i>
                </button>
                <button class="btn btn-icon btn-link btn-danger op-8">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
              <div class="item-list">
                <div class="avatar">
                  <span class="avatar-title rounded-circle border border-white bg-secondary">F</span>
                </div>
                <div class="info-user ms-3">
                  <div class="username">Farrah</div>
                  <div class="status">Marketing</div>
                </div>
                <button class="btn btn-icon btn-link op-8 me-1">
                  <i class="far fa-envelope"></i>
                </button>
                <button class="btn btn-icon btn-link btn-danger op-8">
                  <i class="fas fa-ban"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Top Products</div>
          </div>
          <div class="card-body pb-0">
            <div class="d-flex">
              <div class="avatar">
                <img src="assets/img/logoproduct.svg" alt="..." class="avatar-img rounded-circle" />
              </div>
              <div class="flex-1 pt-1 ms-2">
                <h6 class="fw-bold mb-1">CSS</h6>
                <small class="text-muted">Cascading Style Sheets</small>
              </div>
              <div class="d-flex ms-auto align-items-center">
                <h4 class="text-info fw-bold">+$17</h4>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="d-flex">
              <div class="avatar">
                <img src="assets/img/logoproduct.svg" alt="..." class="avatar-img rounded-circle" />
              </div>
              <div class="flex-1 pt-1 ms-2">
                <h6 class="fw-bold mb-1">J.CO Donuts</h6>
                <small class="text-muted">The Best Donuts</small>
              </div>
              <div class="d-flex ms-auto align-items-center">
                <h4 class="text-info fw-bold">+$300</h4>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="d-flex">
              <div class="avatar">
                <img src="assets/img/logoproduct3.svg" alt="..." class="avatar-img rounded-circle" />
              </div>
              <div class="flex-1 pt-1 ms-2">
                <h6 class="fw-bold mb-1">Ready Pro</h6>
                <small class="text-muted">Bootstrap 5 Admin Dashboard</small>
              </div>
              <div class="d-flex ms-auto align-items-center">
                <h4 class="text-info fw-bold">+$350</h4>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="pull-in">
              <canvas id="topProductsChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-head-row">
              <div class="card-title">Support Tickets</div>
              <div class="card-tools">
                <ul class="nav nav-pills nav-secondary nav-pills-no-bd nav-sm" id="pills-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link" id="pills-today" data-bs-toggle="pill" href="#pills-today" role="tab" aria-selected="true">Today</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" id="pills-week" data-bs-toggle="pill" href="#pills-week" role="tab" aria-selected="false">Week</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="pills-month" data-bs-toggle="pill" href="#pills-month" role="tab" aria-selected="false">Month</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="d-flex">
              <div class="avatar avatar-online">
                <span class="avatar-title rounded-circle border border-white bg-info">J</span>
              </div>
              <div class="flex-1 ms-3 pt-1">
                <h6 class="text-uppercase fw-bold mb-1">
                  Joko Subianto
                  <span class="text-warning ps-3">pending</span>
                </h6>
                <span class="text-muted">I am facing some trouble with my viewport. When i
                  start my</span>
              </div>
              <div class="float-end pt-1">
                <small class="text-muted">8:40 PM</small>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="d-flex">
              <div class="avatar avatar-offline">
                <span class="avatar-title rounded-circle border border-white bg-secondary">P</span>
              </div>
              <div class="flex-1 ms-3 pt-1">
                <h6 class="text-uppercase fw-bold mb-1">
                  Prabowo Widodo
                  <span class="text-success ps-3">open</span>
                </h6>
                <span class="text-muted">I have some query regarding the license issue.</span>
              </div>
              <div class="float-end pt-1">
                <small class="text-muted">1 Day Ago</small>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="d-flex">
              <div class="avatar avatar-away">
                <span class="avatar-title rounded-circle border border-white bg-danger">L</span>
              </div>
              <div class="flex-1 ms-3 pt-1">
                <h6 class="text-uppercase fw-bold mb-1">
                  Lee Chong Wei
                  <span class="text-muted ps-3">closed</span>
                </h6>
                <span class="text-muted">Is there any update plan for RTL version near
                  future?</span>
              </div>
              <div class="float-end pt-1">
                <small class="text-muted">2 Days Ago</small>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="d-flex">
              <div class="avatar avatar-offline">
                <span class="avatar-title rounded-circle border border-white bg-secondary">P</span>
              </div>
              <div class="flex-1 ms-3 pt-1">
                <h6 class="text-uppercase fw-bold mb-1">
                  Peter Parker
                  <span class="text-success ps-3">open</span>
                </h6>
                <span class="text-muted">I have some query regarding the license issue.</span>
              </div>
              <div class="float-end pt-1">
                <small class="text-muted">2 Day Ago</small>
              </div>
            </div>
            <div class="separator-dashed"></div>
            <div class="d-flex">
              <div class="avatar avatar-away">
                <span class="avatar-title rounded-circle border border-white bg-danger">L</span>
              </div>
              <div class="flex-1 ms-3 pt-1">
                <h6 class="text-uppercase fw-bold mb-1">
                  Logan Paul <span class="text-muted ps-3">closed</span>
                </h6>
                <span class="text-muted">Is there any update plan for RTL version near
                  future?</span>
              </div>
              <div class="float-end pt-1">
                <small class="text-muted">2 Days Ago</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include './components/down-all.php' ?>