
<div style="margin-left:auto;margin-right:auto;">
    <style media="all">
        @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,700');
        *{
            margin: 0;
            padding: 0;
            line-height: 1.5;
            font-family: 'Open Sans', sans-serif;
            color: #333542;
        }
        div{
            font-size: 1rem;
        }
        .gry-color *,
        .gry-color{
            color:#878f9c;
        }
        table{
            width: 100%;
        }
        table th{
            font-weight: normal;
        }
        table.padding th{
            padding: .5rem .7rem;
        }
        table.padding td{
            padding: .7rem;
        }
        table.sm-padding td{
            padding: .2rem .7rem;
        }
        .border-bottom td,
        .border-bottom th{
            border-bottom:1px solid #eceff4;
        }
        .text-left{
            text-align:left;
        }
        .text-right{
            text-align:right;
        }
        .small{
            font-size: .85rem;
        }
        .strong{
            font-weight: bold;
        }
    </style>

    <div style="padding: 1.5rem;">
        <table>
            <tr><td class="strong small gry-color">Bill to:</td></tr>
            <tr><td class="strong">{{$donation->name ? ucfirst($donation->name) : "Anonymous"}}</td></tr>
            <tr><td class="gry-color small">Email: {{ucfirst($donation->email)}}</td></tr>
            <tr><td class="gry-color small">Phone: {{$donation->phone}}</td></tr>
            <tr><td class="strong">Cause Title: {{$donation->cause ? $donation->cause->title : ''}}</td></tr>
        </table>
    </div>

    <div style="padding: 1.5rem;">
        <table class="padding text-left small border-bottom">
            <thead>
            <tr class="gry-color" style="background: #eceff4;">
                <th width="25%">Donar Name</th>
                <th width="20%">Donate Amount</th>
                <th width="10%">Currency</th>
                <th width="25%">Payment Method</th>
                <th width="20%">Date</th>
            </tr>
            </thead>
            <tbody class="strong">

                    <tr class="" style="text-align: center">
                        <td>{{$donation->name ? ucfirst($donation->name)  : "Anonymous"}}</td>
                        <td>{{$donation->amount}}</td>
                        <td>{{$donation->currency}}</td>
                        <td>{{$donation->payment_method ? $donation->payment_method  : "Cash"}}</td>
                        <td>{{\Carbon\Carbon::now()->format("d-m-y")}}</td>
                    </tr>
            </tbody>
        </table>
    </div>
</div>
