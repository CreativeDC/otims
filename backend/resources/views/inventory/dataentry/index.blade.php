@extends('layouts.general')
@section('content')
    @isLogin
        <!-- MAIN CONTENT AND INJECTED VIEWS -->
    <div id="main"> </div>
        {{--attaching test id from controller to view --}}
        <input type="text"  name="is_parent" ng-model="is_parent" style="display: none;" ng-init="is_parent = '{!!$is_parent!!}'"/>
        <input type="text"  name="has_parent" ng-model="has_parent" style="display: none;" ng-init="has_parent = '{!!$has_parent!!}'"/>
        <input type="text"  name="has_group" ng-model="has_group" style="display: none;" ng-init="has_group = '{!!$has_group!!}'"/>
        <input type="text"  name="has_child" ng-model="has_child" style="display: none;" ng-init="has_child = '{!!$has_child!!}'"/>
        <input type="text"  name="has_beneficiary" ng-model="has_beneficiary" style="display: none;" ng-init="has_beneficiary = '{!!$has_beneficiary!!}'"/>


        <input type="text"  name="sent_packages_string" ng-model="sent_packages_string" style="display: none;" ng-init="sent_packages_string = '@lang('home.sent_packages_string')'"/>
        <input type="text"  name="package_id_string" ng-model="package_id_string" style="display: none;" ng-init="package_id_string = '@lang('home.package_id_string')'"/>
        <input type="text"  name="title_string" ng-model="title_string" style="display: none;" ng-init="title_string = '@lang('home.title_string')'"/>
        <input type="text"  name="description_string" ng-model="description_string" style="display: none;" ng-init="description_string = '@lang('home.description_string')'"/>
        <input type="text"  name="from_string" ng-model="from_string" style="display: none;" ng-init="from_string = '@lang('home.from_string')'"/>
        <input type="text"  name="to_string" ng-model="to_string" style="display: none;" ng-init="to_string = '@lang('home.to_string')'"/>
        <input type="text"  name="received_status_string" ng-model="received_status_string" style="display: none;" ng-init="received_status_string = '@lang('home.received_status_string')'"/>
        <input type="text"  name="action_string" ng-model="action_string" style="display: none;" ng-init="action_string = '@lang('home.action_string')'"/>
        <input type="text"  name="view_detail_string" ng-model="view_detail_string" style="display: none;" ng-init="view_detail_string = '@lang('home.view_detail_string')'"/>
        <input type="text"  name="send_new_package_string" ng-model="send_new_package_string" style="display: none;" ng-init="send_new_package_string = '@lang('home.send_new_package_string')'"/>
        <input type="text"  name="view_all_packages_string" ng-model="view_all_packages_string" style="display: none;" ng-init="view_all_packages_string = '@lang('home.view_all_packages_string')'"/>
        <input type="text"  name="received_packages_string" ng-model="received_packages_string" style="display: none;" ng-init="received_packages_string = '@lang('home.received_packages_string')'"/>
        <input type="text"  name="send_date_string" ng-model="send_date_string" style="display: none;" ng-init="send_date_string = '@lang('home.send_date_string')'"/>
        <input type="text"  name="status_string" ng-model="status_string" style="display: none;" ng-init="status_string = '@lang('home.status_string')'"/>
        <input type="text"  name="recent_transactions_string" ng-model="recent_transactions_string" style="display: none;" ng-init="recent_transactions_string = '@lang('home.recent_transactions_string')'"/>

        <input type="text"  name="recent_transactions_string" ng-model="recent_transactions_string" style="display: none;" ng-init="recent_transactions_string = '@lang('home.recent_transactions_string')'"/>
        <input type="text"  name="shipment_title_string" ng-model="shipment_title_string" style="display: none;" ng-init="shipment_title_string = '@lang('home.shipment_title_string')'"/>
        <input type="text"  name="shipment_description_string" ng-model="shipment_description_string" style="display: none;" ng-init="shipment_description_string = '@lang('home.shipment_description_string')'"/>
        <input type="text"  name="shipment_recipient_level_string" ng-model="shipment_recipient_level_string" style="display: none;" ng-init="shipment_recipient_level_string = '@lang('home.shipment_recipient_level_string')'"/>
        <input type="text"  name="shipment_recipient_string" ng-model="shipment_recipient_string" style="display: none;" ng-init="shipment_recipient_string = '@lang('home.shipment_recipient_string')'"/>
        <input type="text"  name="shipment_send_time_string" ng-model="shipment_send_time_string" style="display: none;" ng-init="shipment_send_time_string = '@lang('home.shipment_send_time_string')'"/>
        <input type="text"  name="form_grade_one_string" ng-model="form_grade_one_string" style="display: none;" ng-init="form_grade_one_string = '@lang('home.form_grade_one_string')'"/>
        <input type="text"  name="include_grade_shipment_string" ng-model="include_grade_shipment_string" style="display: none;" ng-init="include_grade_shipment_string = '@lang('home.include_grade_shipment_string')'"/>
        <input type="text"  name="id_string" ng-model="id_string" style="display: none;" ng-init="id_string = '@lang('home.id_string')'"/>
        <input type="text"  name="grade_title_string" ng-model="grade_title_string" style="display: none;" ng-init="grade_title_string = '@lang('home.grade_title_string')'"/>
        <input type="text"  name="grade_one_string" ng-model="grade_one_string" style="display: none;" ng-init="grade_one_string = '@lang('home.grade_one_string')'"/>
        <input type="text"  name="grade_two_string" ng-model="grade_two_string" style="display: none;" ng-init="grade_two_string = '@lang('home.grade_two_string')'"/>
        <input type="text"  name="grade_three_string" ng-model="grade_three_string" style="display: none;" ng-init="grade_three_string = '@lang('home.grade_three_string')'"/>
        <input type="text"  name="grade_four_string" ng-model="grade_four_string" style="display: none;" ng-init="grade_four_string = '@lang('home.grade_four_string')'"/>
        <input type="text"  name="grade_five_string" ng-model="grade_five_string" style="display: none;" ng-init="grade_five_string = '@lang('home.grade_five_string')'"/>
        <input type="text"  name="grade_six_string" ng-model="grade_six_string" style="display: none;" ng-init="grade_six_string = '@lang('home.grade_six_string')'"/>
        <input type="text"  name="grade_seven_string" ng-model="grade_seven_string" style="display: none;" ng-init="grade_seven_string = '@lang('home.grade_seven_string')'"/>
        <input type="text"  name="grade_eight_string" ng-model="grade_eight_string" style="display: none;" ng-init="grade_eight_string = '@lang('home.grade_eight_string')'"/>
        <input type="text"  name="grade_nine_string" ng-model="grade_nine_string" style="display: none;" ng-init="grade_nine_string = '@lang('home.grade_nine_string')'"/>
        <input type="text"  name="grade_ten_string" ng-model="grade_ten_string" style="display: none;" ng-init="grade_ten_string = '@lang('home.grade_ten_string')'"/>
        <input type="text"  name="grade_eleven_string" ng-model="grade_eleven_string" style="display: none;" ng-init="grade_eleven_string = '@lang('home.grade_eleven_string')'"/>
        <input type="text"  name="grade_twelve_string" ng-model="grade_twelve_string" style="display: none;" ng-init="grade_twelve_string = '@lang('home.grade_twelve_string')'"/>
        <input type="text"  name="send_shipment_string" ng-model="send_shipment_string" style="display: none;" ng-init="send_shipment_string = '@lang('home.send_shipment_string')'"/>
        <input type="text"  name="details_shipment_string" ng-model="details_shipment_string" style="display: none;" ng-init="details_shipment_string = '@lang('home.details_shipment_string')'"/>
        <input type="text"  name="send_date_string" ng-model="send_date_string" style="display: none;" ng-init="send_date_string = '@lang('home.send_date_string')'"/>
        <input type="text"  name="receive_date_string" ng-model="receive_date_string" style="display: none;" ng-init="receive_date_string = '@lang('home.receive_date_string')'"/>
        <input type="text"  name="language_string" ng-model="language_string" style="display: none;" ng-init="language_string = '@lang('home.language_string')'"/>
        <input type="text"  name="total_string" ng-model="total_string" style="display: none;" ng-init="total_string = '@lang('home.total_string')'"/>
        <input type="text"  name="pending_string" ng-model="pending_string" style="display: none;" ng-init="pending_string = '@lang('home.pending_string')'"/>
        <input type="text"  name="sent_detail_string" ng-model="sent_detail_string" style="display: none;" ng-init="sent_detail_string = '@lang('home.sent_detail_string')'"/>


        <input type="text"  name="total_received_string" ng-model="total_received_string" style="display: none;" ng-init="total_received_string = '@lang('home.total_received_string')'"/>
        <input type="text"  name="total_safe_string" ng-model="total_safe_string" style="display: none;" ng-init="total_safe_string = '@lang('home.total_safe_string')'"/>
        <input type="text"  name="total_damaged_string" ng-model="total_damaged_string" style="display: none;" ng-init="total_damaged_string = '@lang('home.total_damaged_string')'"/>
        <input type="text"  name="total_lost_string" ng-model="total_lost_string" style="display: none;" ng-init="total_lost_string = '@lang('home.total_lost_string')'"/>
        <input type="text"  name="receive_shipment_string" ng-model="receive_shipment_string" style="display: none;" ng-init="receive_shipment_string = '@lang('home.receive_shipment_string')'"/>
        <input type="text"  name="receive_string" ng-model="receive_string" style="display: none;" ng-init="receive_string = '@lang('home.receive_string')'"/>

        @php
            $locale = App::getLocale();
        @endphp

        <input type="text" id="locale_string" name="locale_string" ng-model="locale_string" style="display: none;" ng-init="locale_string = '{{$locale}}'">





  <div ng-view>

        <!-- this is where content will be injected -->
            <section class="content">

                <div class="row">
                    <!-- Left col -->
                    <div class="col-md-8">

                    </div>

                </div>


            </section>
        </div>
    </div>
    @endisLogin
@endsection
@section('page_scripts')
    <script src="<?= asset('app/module/dataentrydashboard.module.js') ?>"></script>
    <script src="<?= asset('app/controllers/dashboard.controller.js') ?>"></script>
@stop
