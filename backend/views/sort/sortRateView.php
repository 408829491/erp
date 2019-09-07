<style>

</style>
<div class="layui-card">
    <div class="layui-card-body">
        <div class="layui-tab-content">
            <div class="layui-form-item">
                <label class="layui-form-label">商品名称</label>
                <div class="layui-input-inline" style="width: 350px;">
                    <div style="margin-top: 7px;"><?= $data[0]['commodity_name'] ?></div>
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">商品描述</label>
                <div class="layui-input-inline" style="width: 350px;">
                    <div style="margin-top: 7px;"><?= $data[0]['notice'] ?></div>
                </div>
            </div>
            <table id="list" lay-filter="list"></table>
            <script type="text/html" id="unit">
                {{#  if(d.is_basics_unit == 1){ }}
                {{ d.unit }}
                {{#  } else { }}
                {{ d.unit }}（{{ d.base_self_ratio }}{{ d.base_unit }}）
                {{#  } }}
            </script>
            <script type="text/html" id="sortStatus">
                {{#  if(d.is_sorted == 0){ }}
                <span style="color:red">未分拣</span>
                {{#  } else { }}
                <span style="color:green">已分拣</span>
                {{#  } }}
             </script>
        </div>
    </div>
</div>

<script src="/admin/plugins/layuiadmin/layui/layui.js"></script>
<script>
    layui.config({
        base: '/admin/plugins/layuiadmin/' // 静态资源所在路径
    }).extend({
        index: 'lib/index' // 主入口模块
    }).use(['index', 'table', 'form'], function () {
        var $ = layui.$
            ,table = layui.table;

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
            , limit: 15
            , limits: [10, 15, 20, 25, 30]
            , text: {
                none: '暂无相关数据'
            }
        });

        // 渲染list
        table.render({
            elem: '#list'
            , data: <?= $jsonData ?>
            , cols: [[
                {field: 'commodity_id', width: 100, title: 'ID', sort: true}
                , {field: 'commodity_name', title: '商品名称', width: 150}
                , {field: 'unit', title: '单位', minWidth: 100, templet: "#unit"}
                , {field: 'notice', title: '商品描述', minWidth: 100}
                , {field: 'is_sorted', title: '分拣状态', minWidth: 100, templet: "#sortStatus"}
            ]]
            , page: false
            , title: "列表"
        });
    })
</script>