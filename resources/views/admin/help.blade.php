{{-- @extends('admin.layouts.app') --}}
@extends('admin.layouts.layout')
@section('content')

<style>
.dynamic-upsell-main-sec {
    border:2px solid #e7ebee;
    padding:30px;
    border-radius:7px;
}
.dynamic-upsell-main-sec h1 , .dynamic-upsell-main-sec p {
    font-size:18px;
    color:#000;
    font-weight:300;
}
.dynamic-upsell-main-sec h1 {
    margin: 18px 0px;
}
.dynamic-upsell-main-sec p {
    font-size:16px;
}
.dynamic-upsell-main-sec h1.main-heading {
    font-size:30px;
    color:#000;
    font-weight:600;
    padding-bottom:15px;
    margin-bottom:15px;
    border-bottom:1px solid #e7ebee;
    margin-top:0px;
}
@media (max-width:575px) {
    .dynamic-upsell-main-sec {
        padding:20px;
    }
    .dynamic-upsell-main-sec h1.main-heading {
        font-size: 28px;
    }
    .dynamic-upsell-main-sec h1 {
        margin: 16px 0px;
        font-size:18px;
    }
    .dynamic-upsell-main-sec p {
        font-size:16px;
        margin-bottom:15px;
    }
}
</style>


<div class="dynamic-upsell-main-sec">
<h1 class="main-heading"> Dynamic Checkout UpSell </h1>

<h1>To Enable Pre-Purchase feature</h1>
<h5>
    <ul>
        <li>
            <p> Go to <strong> Settings > Checkout option </strong> then click on <strong> customize button</strong>. </p>
            <img src="{{asset('app-image/how-to-checkout.png')}}" alt="how-to-checkout" style="max-width: 100% !important; max-height: 100% !important; margin-bottom: 1% !important">
        </li>
        <li>
            <p>Further you can see <strong>Add app</strong> on <strong> bottom left </strong> click on it. </p>
            <img src="{{asset('app-image/add-upsell-on-checkout.png')}}" alt="how-to-checkout" style="max-width: 100% !important; max-height: 100% !important; margin-bottom: 1% !important">
        </li>
        <li>
            <p>Then click on <strong>pre-purchase Dynamic Checkout UpSell</strong> button. Then you can move to set position of this box on checkout page & Save the page. </p>
        </li>
        </li>
        <li>
            <p>Now Pre purchase extention is enable on your store.</p>
        </li>
    </ul>
</h5>

<h1>To Enable Post-Purchase feature</h1>
<h5>
    <ul>
        <li>
            <p> Go to <strong> Settings > Checkout </strong> In this page <strong> scroll down </strong> to <strong> Post-purchase section </strong> after that checked <strong> Dynamic Checkout UpSell </strong> option. </p>
            <img src="{{asset('app-image/upsell-post-purchase.png')}}" alt="how-to-checkout" style="max-width: 100% !important; max-height: 100% !important; margin-bottom: 1% !important">
        </li>
        <li>
            <p>Now Post purchase extention is enable on your store.</p>
        </li>
        <li>
            <p>You can check on thank you page after payment proccess. </p>
        </li>
    </ul>
</h5>
</div>




@endsection
@section('script')
    <script>
        $(window).on('load', function() {
            $('.spinner-loader').hide();
            // setTimeout(function(){ 
            // }, 3000);
        });
    </script>
@endsection