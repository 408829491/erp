<div style="padding: 15px; background-color: #F2F2F2;">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    访总客户数
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">2000</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日新增
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">22</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    待审核
                </div>
                <div class="layui-card-body layuiadmin-card-list">
                    <p class="layuiadmin-big-font">233</p>
                </div>
            </div>
        </div>
        <div class="layui-col-sm6 layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-header">
                    今日未下单用户
                </div>
                <div class="layui-card-body layuiadmin-card-list">

                    <p class="layuiadmin-big-font">232</p>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">
                    七天客户下单Top10
                    <div class="layui-btn-group layuiadmin-btn-group">
                        <a href="javascript:;" class="layui-btn layui-btn-primary layui-btn-xs">查看详情</a>
                    </div>
                </div>
                <div class="layui-card-body">
                    <div style="width: 100%; height: 300px;" id="Shopping">
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">客户增量趋势</div>
                <div class="layui-card-body">
                    <div style="width: 100%; height: 300px;" id="UserSum">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    //注意：折叠面板 依赖 element 模块，否则无法进行功能性操作
    layui.config({
        version: 1,
        base: '/admin/plugins/layuiadmin/lib/extend/' //静态资源所在路径
    }).use(['element', 'echarts', 'carousel'], function () {
        var element = layui.element,
            $ = layui.jquery,
            echarts = layui.echarts;

        // 基于准备好的dom，初始化echarts实例
        var Shopping = echarts.init(document.getElementById('Shopping')),
            UserSum = echarts.init(document.getElementById('UserSum'));
        // 指定图表的配置项和数据
        var optionShopping = {
            title: {
                text: '订单总额'
            },
            tooltip: {},
            legend: {
                data: ['订单额','订单数量']
            },
            xAxis: {
                data: ['天河农家乐', '临安香格里大酒店', '小方出庄', '华龙山庄', '芳芳美食', '卡其堡餐饮', '临安老家酒楼']
            },
            yAxis: {type: 'value'},
            series: [{
                name: '订单额',
                type: 'bar',//柱状
                data: [820, 500, 300, 120, 100, 50, 6],
                itemStyle:{
                    normal:{//柱子颜色
                        color:'#4ad2ff'
                    }
                },
            },{
                name: '订单数量',
                type: 'bar',//柱状
                data: [820, 500, 300, 120, 100, 50, 6],
                itemStyle:{
                    normal:{//柱子颜色
                        color:'red'
                    }
                },
            }]
        }, optionUserSum = {
            title: {
                text: '用户数量'
            },
            tooltip: {
                trigger: 'axis'//悬浮显示对比
            },
            legend: {//顶部显示 与series中的数据类型的name一致
                data: ['用户数据']
            },
            toolbox: {
                feature: {
                    saveAsImage: {}//保存图片下载
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,//起点开始
                data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                name: '用户增量',
                smooth:true,//曲线
                type: 'line',//线性
                areaStyle: {
                    color: ['rgba(70,220,230,.8)']
                },//区域颜色
                lineStyle:{//线条颜色
                    color:'#00FF00'
                }, itemStyle : {
                    normal : {//折点颜色
                        color:'#000'
                    }
                },
                data: [620, 732, 701, 734, 1090, 1130, 1120],
            }]
        };
        // 使用刚指定的配置项和数据显示图表。
        Shopping.setOption(optionShopping);
        UserSum.setOption(optionUserSum);
        window.onresize = function () {
            Shopping.resize();
            Notice.resize();
            NoticeAll.resize();
            UserSum.resize();
        };
    });
</script>
