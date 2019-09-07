  <div class="layui-fluid">
    <div class="layui-row layui-col-space15">
      
      <div class="layui-col-sm6 layui-col-md4">
        <div class="layui-card">
          <div class="layui-card-header">
            订单总量
          </div>
          <div class="layui-card-body layuiadmin-card-list">
            <p class="layuiadmin-big-font"><?=$order['total_orders']?></p>
            <p>
              今日订单
              <span class="layuiadmin-span-color"><?=$order['total_today_orders']?> <i class="layui-inline layui-icon layui-icon-flag"></i></span>
            </p>
          </div>
        </div>
      </div>
      <div class="layui-col-sm6 layui-col-md4">
        <div class="layui-card">
          <div class="layui-card-header">
            客户总量
            <span class="layui-badge layui-bg-cyan layuiadmin-badge">月</span>
          </div>
          <div class="layui-card-body layuiadmin-card-list">
            <p class="layuiadmin-big-font"><?=$users['total_users']?></p>
            <p>
              今日新增
              <span class="layuiadmin-span-color"><?=$users['total_today_users']?> <i class="layui-inline layui-icon layui-icon-face-smile-b"></i></span>
            </p>
          </div>
        </div>
      </div>
      <div class="layui-col-sm6 layui-col-md4">
        <div class="layui-card">
          <div class="layui-card-header">
              总营业额
            <span class="layui-badge layui-bg-green layuiadmin-badge">年</span>
          </div>
          <div class="layui-card-body layuiadmin-card-list">

            <p class="layuiadmin-big-font"><?=$order['total_prices']?></p>
            <p>
              今日金额
              <span class="layuiadmin-span-color"><?=$order['total_today_prices']?> <i class="layui-inline layui-icon layui-icon-dollar"></i></span>
            </p>
          </div>
        </div>
      </div>
<!--      <div class="layui-col-sm6 layui-col-md3">-->
<!--        <div class="layui-card">-->
<!--          <div class="layui-card-header">-->
<!--            利润-->
<!--            <span class="layui-badge layui-bg-orange layuiadmin-badge">月</span>-->
<!--          </div>-->
<!--          <div class="layui-card-body layuiadmin-card-list">-->
<!---->
<!--            <p class="layuiadmin-big-font">63523.22</p>-->
<!--            <p>-->
<!--              最近一个月-->
<!--              <span class="layuiadmin-span-color">15% <i class="layui-inline layui-icon layui-icon-user"></i></span>-->
<!--            </p>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>   -->
      <div class="layui-col-sm12">
        <div class="layui-card">
          <div class="layui-card-header">
              营业数据趋势图
            <div class="layui-btn-group layuiadmin-btn-group">
              <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">最近7天</a>
              <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">最近15天</a>
              <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">最近30天</a>
            </div>
          </div>
          <div class="layui-card-body">
            <div class="layui-row">
              <div class="layui-col-sm12">
                  <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-pagetwo">
                    <div id="tend" style="width: 100%; height: 360px;"></div>
                  </div>
              </div>
<!--              <div class="layui-col-sm4">-->
<!--                <div class="layuiadmin-card-list">-->
<!--                  <p class="layuiadmin-normal-font">客户增长数</p>-->
<!--                  <span>同上期增长</span>-->
<!--                  <div class="layui-progress layui-progress-big" lay-showPercent="yes">-->
<!--                    <div class="layui-progress-bar" lay-percent="30%"></div>-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="layuiadmin-card-list">-->
<!--                  <p class="layuiadmin-normal-font">门店增长数</p>-->
<!--                  <span>同上期增长</span>-->
<!--                  <div class="layui-progress layui-progress-big" lay-showPercent="yes">-->
<!--                    <div class="layui-progress-bar" lay-percent="20%"></div>-->
<!--                  </div>-->
<!--                </div>-->
<!--                <div class="layuiadmin-card-list">-->
<!--                  <p class="layuiadmin-normal-font">毛利润增长</p>-->
<!--                  <span>同上期增长</span>-->
<!--                  <div class="layui-progress layui-progress-big" lay-showPercent="yes">-->
<!--                    <div class="layui-progress-bar" lay-percent="60%"></div>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
            </div>
          </div>
        </div>
      </div>
      <div class="layui-col-sm4">

              <div class="layui-card">
                  <div class="layui-card-header">大类排行</div>
                  <div class="layui-card-body">
                      <div class="layui-tab-content">
                          <div class="layui-tab-item layui-show">
                              <div id="category" style="width:100%;height:310px;"></div>
                          </div>
                      </div>
                  </div>
              </div>
      </div>
      <div class="layui-col-sm8">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm6">
                <div class="layui-card">
                    <div class="layui-card-header">商品排行</div>
                    <div class="layui-card-body">
                        <div class="layui-tab-content">
                            <div class="layui-tab-item layui-show">
                                <table id="commodity"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          <div class="layui-col-sm6">
            <div class="layui-card">
              <div class="layui-card-header">客户排行</div>
              <div class="layui-card-body">
                <table class="layui-table layuiadmin-page-table" lay-skin="line">
                  <thead>
                    <tr>
                      <th>客户名称</th>
                      <th>订购总金额</th>
                      <th>订单数</th>
                    </tr> 
                  </thead>
                  <tbody>

                   <?php
                    foreach($orderUser as $v){
                        echo "<tr>
                      <td><span class=\"first\">".$v['nick_name']."</span></td>
                      <td><i class=\"layui-icon layui-icon-rmb\">".$v['total_price']."</i></td>
                      <td><span>".$v['total_count']."</span></td>
                    </tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
      </div>
        
      </div>
    </div>

  <script src="plugins/layuiadmin/layui/layui.js"></script>
  <script>
  layui.config({
    base: 'plugins/layuiadmin/' //静态资源所在路径
  }).extend({
    index: 'lib/index' //主入口模块
  }).use(['index', 'sample','echarts','table'], function(){
      var echarts = layui.echarts
          , table = layui.table
          , admin = layui.admin;
      var tend = echarts.init(document.getElementById('tend'));
      var cate = echarts.init(document.getElementById('category'));
      var data = <?=json_encode($business)?>;
      var data2 = <?=json_encode($orderCategory)?>;
      var xAxis = [];
      var series_price = [];
      var series_num = [];
      var series_profit = [];
      for (var i = 0; i < data.length; i++) {
          xAxis[i] = data[i]['date'];
          series_price[i] = data[i]['total_price'];
          series_num[i] = data[i]['order_count'];
          series_profit[i] = data[i]['total_profit'];
      }

      var legend = [];
      var series = [];
      for (var j = 0; j < data2.length; j++) {
          legend[j] = data2[j]['name'];
          series[j] = {"name": data2[j]['name'], "value": data2[j]['total_count']}
      }

      var option = {
          title : {
              text: '营业数据趋势图',
              subtext: '营业数据'
          },
          tooltip : {
              trigger: 'axis'
          },
          legend: {
              data:['订单数','营业额']
          },
          toolbox: {
              show : true,
              feature : {
                  mark : {show: false},
                  dataView : {show: true, readOnly: false},
                  magicType : {show: true, type: ['line', 'bar']},
                  restore : {show: true},
                  saveAsImage : {show: true}
              }
          },
          calculable : true,
          xAxis : [
              {
                  type : 'category',
                  data : xAxis
              }
          ],
          yAxis : [
              {
                  type : 'value'
              }
          ],
          series : [
              {
                  name:'订单数',
                  type:'bar',
                  data:series_num,
                  markPoint : {
                      data : [
                          {type : 'max', name: '最大值'},
                          {type : 'min', name: '最小值'}
                      ]
                  },
                  markLine : {
                      data : [
                          {type : 'average', name: '平均值'}
                      ]
                  }
              },
              {
                  name:'营业额',
                  type:'bar',
                  data:series_price,
                  markPoint : {
                      data : [
                          {name : '年最高', value : 28182.2, xAxis: 7, yAxis: 183, symbolSize:18},
                          {name : '年最低', value : 2.3, xAxis: 11, yAxis: 3}
                      ]
                  },
                  markLine : {
                      data : [
                          {type : 'average', name : '平均值'}
                      ]
                  }
              }
              ,
              {
                  name:'利润',
                  type:'bar',
                  data:series_profit,
                  markPoint : {
                      data : [
                          {name : '年最高', value : 1182.2, xAxis: 7, yAxis: 183, symbolSize:18},
                          {name : '年最低', value : 2.3, xAxis: 11, yAxis: 3}
                      ]
                  },
                  markLine : {
                      data : [
                          {type : 'average', name : '平均值'}
                      ]
                  }
              }
          ]
      };


      cateOption = {
          title : {
              text: '大类排行',
              x:'center'
          },
          tooltip : {
              trigger: 'item',
              formatter: "{a} <br/>{b} : {c} ({d}%)"
          },
          legend: {
              orient : 'vertical',
              x : 'left',
              data:legend
          },
          toolbox: {
              show : false,
              feature : {
                  mark : {show: true},
                  dataView : {show: true, readOnly: false},
                  magicType : {
                      show: true,
                      type: ['pie', 'funnel'],
                      option: {
                          funnel: {
                              x: '25%',
                              width: '50%',
                              funnelAlign: 'left',
                              max: 1548
                          }
                      }
                  },
                  restore : {show: true},
                  saveAsImage : {show: true}
              }
          },
          calculable : true,
          series : [
              {
                  name:'访问来源',
                  type:'pie',
                  radius : '40%',
                  center: ['60%', '60%'],
                  data:series
              }
          ]
      };



      // 使用刚指定的配置项和数据显示图表。
      tend.setOption(option);
      cate.setOption(cateOption);
      window.onresize = function () {
          tend.resize();
      };


      table.render({
          elem: '#commodity'
          ,data:<?=json_encode($orderDetail)?>
          ,title: '客户对账单'
          ,cols: [[
              {field:'commodity_name', title:'商品名称',width:150}
              ,{field:'name', title:'商品分类'}
              ,{field:'total_count', title:'销售量'}
          ]]
          ,page: false
      });


  });



  </script>