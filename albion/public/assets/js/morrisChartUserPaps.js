new Morris.Donut({
    element: 'papsTotalDC',
    data: [
        { label: '{$userName}', value: {$papsTotalUser} },
        { label: 'Others' , value: {$papsUserRatio} }
    ],
    resize: true
});
new Morris.Donut({
    element: 'papsTypeDC',
    data: [
        { label: 'PvP', value: {$papsUserPvP} },
        { label: 'PvE' , value: {$papsUserPvE} }
    ],
    resize: true,
    colors: [
        '#B20000',
        '#297A29'
    ]
});
new Morris.Area({
    element: 'papsMonthly',
    data: {$papsMonthlyData},
    xkey: 'date',
    ykeys: ['papsTotal', 'papsUser'],
    xLabels:'day',
    labels: ['Guild', '{$userName}'],
    dateFormat: function (x) { return new Date(x).toLocaleDateString('fr-FR'); }
});