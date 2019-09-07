<div class="layui-card">
    <div class="layui-form layui-card-header layuiadmin-card-header-auto">
        <div class="layui-form-item">
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <input type="text" readonly name="delivery_date" class="layui-input" id="laydate-create-date" placeholder="发货日期">
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <input type="text" name="searchText" placeholder="请输入客户名称" autocomplete="off" class="layui-input">
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <select name="line_id">
                        <option value="">全部线路</option>
                        <?php foreach ($lineData as $item) : ?>
                            <option value="<?= $item->id ?>"><?= $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <div class="layui-input-inline">
                    <select name="sortStatus">
                        <option value="">分拣状态</option>
                        <option value="0">未分拣</option>
                        <option value="1">分拣中</option>
                        <option value="2">已分拣</option>
                    </select>
                </div>
            </div>
            <div class="layui-inline">
                <button class="layui-btn layuiadmin-btn-list" lay-submit lay-filter="search">
                    <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="layui-card-body">
        <table id="list" lay-filter="list"></table>
        <script type="text/html" id="sortStatus">
            {{#  if(d.sortedNum == 0){ }}
            <span style="color:red">未分拣</span>
            {{#  } else if (parseInt(d.sortedNum) < parseInt(d.totalNum)) { }}
            <span style="color:#ec971f">分拣中</span>
            {{#  } else { }}
            <span style="color:green">已分拣</span>
            {{#  } }}
         </script>
        <script type="text/html" id="sortRate">
            <div class="layui-progress layui-progress-big" lay-showPercent="yes" style="margin-top: 5px;">
                <div class="layui-progress-bar layui-bg-green" lay-percent="{{ Math.round((d.sortedNum / d.totalNum) * 100) }}%"></div>
            </div>
         </script>
        <script type="text/html" id="operation">
            <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="view" style="background-color: #77CF20"><i
                        class="layui-icon layui-icon-edit"></i>查看</a>
        </script>
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' //静态资源所在路径
    }).extend({
        index: 'lib/index' //主入口模块
    }).use(['index', 'table', 'laydate', 'element'], function () {
        var table = layui.table
            , form = layui.form
            , laydate = layui.laydate
            , element = layui.element
            , $ = layui.$;

        //table设置默认参数
        table.set({
            page: true
            , parseData: function (res) {
                return {
                    "code": res.code,
                    "msg": res.msg,
                    "count": res.data.total,
                    "data": res.data.list,
                }
            }
            , request: {
                pageName: 'pageNum',
                limitName: 'pageSize'
            }
            , response: {
                statusCode: 200
            }
            , toolbar: true
            , limit: 100
            , limits: [100, 200, 300]
            , text: {
                none: '暂无相关数据'
            }
        });

        var tomorrow = new Date(new Date().getTime() + 24 * 60 * 60 * 1000);

        // 格式化日期
        function formatDate(date) {
            var month = date.getMonth() < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
            var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
            return date.getFullYear() + '-' + month + '-' + day;
        }

        //日期范围
        laydate.render({
            elem: '#laydate-create-date'
            ,value:new Date(tomorrow)
            , done: function () {
                $('[lay-filter = search]').trigger('click');
            }
        });

        //监听搜索
        form.on('submit(search)', function (data) {
            var field = {};
            field.filterProperty=JSON.stringify(data.field);

            //执行重载
            table.reload('list', {
                where: field
            });
        });

        // 渲染list
        table.render({
            elem: '#list'
            , url: '/admin/sort/sort-rate-by-user-data'
            , cols: [[
                {field: 'user_id', width: 100, title: 'ID', sort: true}
                , {field: 'nick_name', title: '客户昵称', width: 150}
                , {field: 'line_name', title: '线路', minWidth: 100}
                , {title: '分拣进度', minWidth: 100, templet: "#sortRate"}
                , {title: '分拣状态', minWidth: 100, templet: "#sortStatus"}
                , {title: '操作', minWidth: 300, align: 'center', fixed: 'right', toolbar: '#operation'}
            ]]
            , where: {filterProperty: JSON.stringify({delivery_date: formatDate(tomorrow)})}
            , title: "列表"
            , done: function () {
                element.init();
            }
        });

        //监听工具条
        table.on('tool(list)', function (obj) {
            var data = obj.data;
            if (obj.event === 'view') {
                // 查看分拣情况
                var selectDate = $('[name = delivery_date]').val();
                layer.open({
                    type: 2
                    , title: [data.nick_name + ' 分拣情况', 'font-size: 14px;font-weight: 700;']
                    , content: '/admin/sort/sort-rate-by-user-view?userId=' + data.user_id + '&delivery_date=' + selectDate
                    , maxmin: true
                    , area: ['80%', '80%']
                    , btn: ['关闭']
                });
            }
        });

    });

</script>