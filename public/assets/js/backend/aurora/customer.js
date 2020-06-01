define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'aurora/customer/index' + location.search,
                    add_url: 'aurora/customer/add',
                    edit_url: 'aurora/customer/edit',
                    del_url: 'aurora/customer/del',
                    multi_url: 'aurora/customer/multi',
                    table: 'aurora_customer',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'customerUid', title: __('Customeruid')},
                        {field: 'categoryName', title: __('Categoryname')},
                        {field: 'number', title: __('Number')},
                        {field: 'name', title: __('Name')},
                        {field: 'point', title: __('Point'), operate:'BETWEEN'},
                        {field: 'discount', title: __('Discount'), operate:false},
                        {field: 'balance', title: __('Balance'), operate:'BETWEEN'},
                        {field: 'phone', title: __('Phone')},
                        {field: 'birthday', title: __('Birthday'), operate:false},
                        {field: 'qq', title: __('Qq'), operate:false},
                        {field: 'email', title: __('Email'), operate:false},
                        {field: 'address', title: __('Address'), operate:false},
                        {field: 'remarks', title: __('Remarks'), operate:false},
                        {field: 'createStoreAppIdOrAccount', title: __('Createstoreappidoraccount')},
                        {field: 'onAccount', title: __('Onaccount'),searchList:{'0':'不允许','1':'允许'},formatter: Table.api.formatter.status},
                        {field: 'enable', title: __('Enable'),searchList:{'-1':'删除','0':'禁用','1':'可用'},formatter: Table.api.formatter.status},
                        {field: 'createdDate', title: __('Createddate'), operate:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});