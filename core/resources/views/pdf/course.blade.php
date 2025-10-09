<!DOCTYPE html>
<head lang="en">
  <meta charset="UTF-8">
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >
  <meta
    http-equiv="X-UA-Compatible"
    content="ie=edge"
  >
  <link rel="stylesheet" href="{{asset('assets/front/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{asset('assets/front/css/style.css')}}">
  <title>Course Order Invoice</title>
</head>

<body>
  <div class="course-order-confirmation">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div
            class="logo text-center"
            style="margin-bottom: 20px;"
          >
            <img
              src="{{asset('assets/front/img/' . $logo)}}"
              alt="Company Logo"
            >
          </div>
  
          <div
            class="course-confirmation-message bg-primary"
            style="padding: 1px 0px;"
          >
            <h2 class="text-center text-light">
              <strong>{{__('COURSE PURCHASE INVOICE')}}</strong>
            </h2>
          </div>
  
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <h3><strong>Purchase Details</strong></h3>
              </div>
              <table class="table table-striped table-bordered">
                <tbody>
                  <tr>
                    <th scope="row">Order Number:</th>
                    <td>{{'#' . $order_info->order_number}}</td>
                  </tr>
                  <tr>
                    <th scope="row">Course Name:</th>
                    <td>{{$order_info->course->title}}</td>
                  </tr>
                  <tr>
                    <th scope="row">Purchase Date:</th>
                    <td>{{$order_info->created_at->format('d-m-Y')}}</td>
                  </tr>
                  <tr>
                    <th scope="row">Payment Method:</th>
                    <td class="text-capitalize">{{$order_info->payment_method}}</td>
                  </tr>
                  <tr>
                    <th scope="row">Payment Status:</th>
                    <td class="text-capitalize">{{$order_info->payment_status}}</td>
                  </tr>
                  <tr>
                    <th scope="row">Course Price:</th>
                    <td class="text-capitalize">
                      <span>{{$bse->base_currency_symbol_position == 'left' ? $bse->base_currency_text : ''}}</span> {{$order_info->current_price}} <span>{{$bse->base_currency_symbol_position == 'right' ? $bse->base_currency_text : ''}}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
