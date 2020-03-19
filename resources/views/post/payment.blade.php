{{--
 * LaraClassified - Classified Ads Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: http://www.bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from Codecanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@section('content')
	{!! csrf_field() !!}
	<input type="hidden" id="postId" name="post_id" value="{{ $post->id }}">
	
	@if (Session::has('flash_notification'))
		@include('common.spacer')
		<?php $paddingTopExists = true; ?>
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					@include('flash::message')
				</div>
			</div>
		</div>
		<?php Session::forget('flash_notification.message'); ?>
	@endif
	
	<div class="main-container">
		
		<?php if (\App\Models\Advertising::where('slug', 'top')->count() > 0): ?>
			@include('layouts.inc.advertising.top', ['paddingTopExists' => (isset($paddingTopExists)) ? $paddingTopExists : false])
		<?php
			$paddingTopExists = false;
		endif;
		?>
		@include('common.spacer')
		
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					
					<nav aria-label="breadcrumb" role="navigation" class="pull-left">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ lurl('/') }}"><i class="icon-home fa"></i></a></li>
							<li class="breadcrumb-item"><a href="{{ lurl('/') }}">{{ config('country.name') }}</a></li>
							@if (!empty($post->category->parent))
								<li class="breadcrumb-item">
									<a href="{{ \App\Helpers\UrlGen::category($post->category->parent) }}">
										{{ $post->category->parent->name }}
									</a>
								</li>
								@if ($post->category->parent->id != $post->category->id)
									<li class="breadcrumb-item">
										<a href="{{ \App\Helpers\UrlGen::category($post->category, 1) }}">
											{{ $post->category->name }}
										</a>
									</li>
								@endif
							@else
								<li class="breadcrumb-item">
									<a href="{{ \App\Helpers\UrlGen::category($post->category) }}">
										{{ $post->category->name }}
									</a>
								</li>
							@endif
							<li class="breadcrumb-item active" aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 70) }}</li>
						</ol>
					</nav>
				</div>
			</div>
		</div>
		
		<div class="container">
			<div class="row">
				<div class="col-lg-8 page-content col-thin-right">
					<div class="inner inner-box items-details-wrapper pb-0">
						<h2 class="enable-long-words">
							<strong>
								<a href="{{ \App\Helpers\UrlGen::post($post) }}" title="{{ $post->title }}">
									{{ $post->title }}
                                </a>
                            </strong>
							<small class="label label-default adlistingtype">{{ $post->postType->name }}</small>
							@if ($post->featured==1 and !empty($post->latestPayment))
								@if (isset($post->latestPayment->package) and !empty($post->latestPayment->package))
									<i class="icon-ok-circled tooltipHere" style="color: {{ $post->latestPayment->package->ribbon }};" title="" data-placement="right"
									   data-toggle="tooltip" data-original-title="{{ $post->latestPayment->package->short_name }}"></i>
								@endif
                            @endif
						</h2>
						<span class="info-row">
							<span class="date"><i class="icon-clock"> </i> {{ $post->created_at_ta }} </span> -&nbsp;
							<span class="category">{{ (!empty($post->category->parent)) ? $post->category->parent->name : $post->category->name }}</span> -&nbsp;
							<span class="item-location"><i class="fas fa-map-marker-alt"></i> {{ $post->city->name }} </span> -&nbsp;
							<span class="category">
								<i class="icon-eye-3"></i>&nbsp;
								{{ \App\Helpers\Number::short($post->visits) }} {{ trans_choice('global.count_views', getPlural($post->visits)) }}
							</span>
						</span>
						
						<div class="posts-image">
							<?php $titleSlug = \Illuminate\Support\Str::slug($post->title); ?>
							@if (!in_array($post->category->type, ['not-salable']))
								<h1 class="pricetag">
									@if ($post->price > 0)
										{!! \App\Helpers\Number::money($post->price) !!}
									@else
										{!! \App\Helpers\Number::money(' --') !!}
									@endif
								</h1>
							@endif
							@if (count($post->pictures) > 0)
								<ul class="bxslider">
									@foreach($post->pictures as $key => $image)
										<li><img src="{{ imgUrl($image->filename, 'big') }}" alt="{{ $titleSlug . '-big-' . $key }}"></li>
									@endforeach
								</ul>
								<div class="product-view-thumb-wrapper">
									<ul id="bx-pager" class="product-view-thumb">
									@foreach($post->pictures as $key => $image)
										<li>
											<a class="thumb-item-link" data-slide-index="{{ $key }}" href="">
												<img src="{{ imgUrl($image->filename, 'small') }}" alt="{{ $titleSlug . '-small-' . $key }}">
											</a>
										</li>
									@endforeach
									</ul>
								</div>
							@else
								<ul class="bxslider">
									<li><img src="{{ imgUrl(config('larapen.core.picture.default'), 'big') }}" alt="img"></li>
								</ul>
								<div class="product-view-thumb-wrapper">
									<ul id="bx-pager" class="product-view-thumb">
										<li>
											<a class="thumb-item-link" data-slide-index="0" href="">
												<img src="{{ imgUrl(config('larapen.core.picture.default'), 'small') }}" alt="img">
											</a>
										</li>
									</ul>
								</div>
							@endif
						</div>
						<!--posts-image-->
					</div>
					<!--/.items-details-wrapper-->
				</div>
				<!--/.page-content-->

				<div class="col-lg-4 page-sidebar-right">
					<aside>
						<div class="card card-user-info sidebar-card">
                            <div class="card-header">{{ t('Order ID') }}: <br /><b>{{$orderId}}</b></div>
                        
                            <div class="card-content">
                                <div class="card-body text-left">
                                    
                                    <!-- show payment method custom fields -->
                                    @if (isset($customFields) and $customFields->count() > 0)
                                        <div class="row" id="customFields">
                                            <div class="col-xl-12">
                                                <div class="row pl-2 pr-2">
                                                    @foreach($customFields as $field)
                                                        <?php
                                                        if (in_array($field->type, ['radio', 'select'])) {
                                                            if (is_numeric($field->default)) {
                                                                $option = \App\Models\FieldOption::findTrans($field->default);
                                                                if (!empty($option)) {
                                                                    $field->default = $option->value;
                                                                }
                                                            }
                                                        }
                                                        if (in_array($field->type, ['checkbox'])) {
                                                            $field->default = ($field->default == 1) ? t('Yes') : t('No');
                                                        }
                                                        ?>
                                                        @if ($field->type == 'file')
                                                            <div class="detail-line col-xl-12 pb-2 pl-1 pr-1">
                                                                <div class="rounded-small ml-0 mr-0 p-2">
                                                                    <span class="detail-line-label" style="padding-top: 8px;">{{ $field->name }}</span>
                                                                    <span class="detail-line-value">
                                                                        <a class="btn btn-default" href="{{ fileUrl($field->default) }}" target="_blank">
                                                                            <i class="icon-attach-2"></i> {{ t('Download') }}
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @else
                                                            @if (!is_array($field->default))
                                                                <div class="detail-line col-sm-6 col-xs-12 pb-2 pl-1 pr-1">
                                                                    <div class="rounded-small p-2">
                                                                        <span class="detail-line-label">{{ $field->name }}</span>
                                                                        <span class="detail-line-value">{{ $field->default }}</span>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                @if (count($field->default) > 0)
                                                                <div class="detail-line col-xl-12 pb-2 pl-1 pr-1">
                                                                    <div class="rounded-small p-2">
                                                                        <span>{{ $field->name }}:</span>
                                                                        <div class="row m-0 p-2">
                                                                            @foreach($field->default as $valueItem)
                                                                                @continue(!isset($valueItem->value))
                                                                                <div class="col-sm-4 col-xs-6 col-xxs-12">
                                                                                    <div class="m-0">
                                                                                        <i class="fa fa-check"></i> {{ $valueItem->value }}
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!--/show payment method custom fields-->

                                    <div class="grid-col">
                                        {{ t('after payment notific text') }}
                                    </div>

                                    <form role="form" method="POST" action="{{ lurl('posts/' . $post->id . '/buy/confirm') }}" enctype="multipart/form-data">
                                        {!! csrf_field() !!}
                                            
                                        <input type="hidden" name="from_name" value="{{ auth()->user()->name }}">
                                        <input type="hidden" name="from_email" value="{{ auth()->user()->email }}"> 
                                        <input type="hidden" name="from_phone" value="{{ auth()->check() ? auth()->user()->phone : '' }}">                      
                                        @include('layouts.inc.tools.recaptcha', ['label' => true])
                                        <input type="hidden" name="message" value="Ordine Numero: {{$orderId}} Ricevuto">
                                        <input type="hidden" name="country_code" value="{{ config('country.code') }}">
                                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                                        <input type="hidden" name="messageForm" value="1">
                                        <button type="submit" class="btn btn-default btn-block"><i class="icon-mail-2"></i> {{ t('Send message') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
					</aside>
				</div>
			</div>

		</div>
				
	</div>
@endsection

@section('after_styles')
	<!-- bxSlider CSS file -->
	@if (config('lang.direction') == 'rtl')
		<link href="{{ url('assets/plugins/bxslider/jquery.bxslider.rtl.css') }}" rel="stylesheet"/>
	@else
		<link href="{{ url('assets/plugins/bxslider/jquery.bxslider.css') }}" rel="stylesheet"/>
	@endif
@endsection

@section('after_scripts')
    @if (config('services.googlemaps.key'))
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.googlemaps.key') }}" type="text/javascript"></script>
    @endif

	<!-- bxSlider Javascript file -->
	<script src="{{ url('assets/plugins/bxslider/jquery.bxslider.min.js') }}"></script>
    
	<script>
		/* Favorites Translation */
        var lang = {
            labelSavePostSave: "{!! t('Save ad') !!}",
            labelSavePostRemove: "{!! t('Remove favorite') !!}",
            loginToSavePost: "{!! t('Please log in to save the Ads.') !!}",
            loginToSaveSearch: "{!! t('Please log in to save your search.') !!}",
            confirmationSavePost: "{!! t('Post saved in favorites successfully !') !!}",
            confirmationRemoveSavePost: "{!! t('Post deleted from favorites successfully !') !!}",
            confirmationSaveSearch: "{!! t('Search saved successfully !') !!}",
            confirmationRemoveSaveSearch: "{!! t('Search deleted successfully !') !!}"
        };
		
		$(document).ready(function () {
			/* bxSlider - Main Images */
			$('.bxslider').bxSlider({
				speed: 1000,
				pagerCustom: '#bx-pager',
				adaptiveHeight: true,
				onSlideAfter: function ($slideElement, oldIndex, newIndex) {
					@if (!userBrowser('Chrome'))
						$('#bx-pager li:not(.bx-clone)').eq(newIndex).find('a.thumb-item-link').addClass('active');
					@endif
				}
			});
			
			/* bxSlider - Thumbnails */
			@if (userBrowser('Chrome'))
				$('#bx-pager').addClass('m-3');
				$('#bx-pager .thumb-item-link').unwrap();
			@else
				var thumbSlider = $('.product-view-thumb').bxSlider(bxSliderSettings());
				$(window).on('resize', function() {
					thumbSlider.reloadSlider(bxSliderSettings());
				});
			@endif
			
			@if (config('settings.single.show_post_on_googlemap'))
				/* Google Maps */
				getGoogleMaps(
				'{{ config('services.googlemaps.key') }}',
				'{{ (isset($post->city) and !empty($post->city)) ? addslashes($post->city->name) . ',' . config('country.name') : config('country.name') }}',
				'{{ config('app.locale') }}'
				);
			@endif
            
			/* Keep the current tab active with Twitter Bootstrap after a page reload */
            /* For bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line */
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                /* save the latest tab; use cookies if you like 'em better: */
                localStorage.setItem('lastTab', $(this).attr('href'));
            });
            /* Go to the latest tab, if it exists: */
            var lastTab = localStorage.getItem('lastTab');
            if (lastTab) {
                $('[href="' + lastTab + '"]').tab('show');
            }
		});
		
		/* bxSlider - Initiates Responsive Carousel */
		function bxSliderSettings()
		{
			var smSettings = {
				slideWidth: 65,
				minSlides: 1,
				maxSlides: 4,
				slideMargin: 5,
				adaptiveHeight: true,
				pager: false
			};
			var mdSettings = {
				slideWidth: 100,
				minSlides: 1,
				maxSlides: 4,
				slideMargin: 5,
				adaptiveHeight: true,
				pager: false
			};
			var lgSettings = {
				slideWidth: 100,
				minSlides: 3,
				maxSlides: 6,
				pager: false,
				slideMargin: 10,
				adaptiveHeight: true
			};
			
			if ($(window).width() <= 640) {
				return smSettings;
			} else if ($(window).width() > 640 && $(window).width() < 768) {
				return mdSettings;
			} else {
				return lgSettings;
			}
		}
	</script>
@endsection