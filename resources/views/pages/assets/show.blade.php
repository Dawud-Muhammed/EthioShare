@extends('layouts.app')

@section('content')
@include('layouts.nav-header')

<div x-data="assetDetail()" class="bg-gray-50">
  <!-- Hero -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Left: Gallery + Main content (col-span 7) -->
      <div class="lg:col-span-7">
        <div class="bg-white rounded-lg shadow">
          <!-- Gallery -->
          <div class="relative">
            <button @click="toggleFavorite" :class="favorite ? 'text-red-500' : 'text-gray-400'" class="absolute z-20 right-4 top-4 bg-white/80 p-2 rounded-full">
              <svg xmlns="http://www.w3.org/2000/svg" :class="favorite ? 'fill-current' : 'stroke-current'" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 21s-7-4.35-9-7.5C1.5 10 4 6 7.5 6 9.2 6 10 7 12 9c2-2 2.8-3 4.5-3C20 6 22.5 10 21 13.5 19 16.65 12 21 12 21z" />
              </svg>
            </button>

            <div class="w-full h-[400px] bg-gray-100 flex items-center justify-center overflow-hidden rounded-t-lg">
              <img :src="images[curIndex].url" :alt="images[curIndex].alt" class="w-full h-full object-cover" style="width:600px; height:400px;" @click="openLightbox(curIndex)" />
            </div>

            <!-- Prev / Next -->
            <button @click="prev" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 p-2 rounded-full">
              <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button @click="next" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 p-2 rounded-full">
              <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

            <div class="absolute left-4 bottom-4 bg-white/90 px-3 py-1 rounded-full text-sm text-gray-700"> <span x-text="curIndex+1"></span> of <span x-text="images.length"></span> </div>
          </div>

          <!-- Thumbnails -->
          <div class="p-4 border-t">
            <div class="flex gap-2 overflow-x-auto">
              <template x-for="(img, idx) in images" :key="idx">
                <button @click="goTo(idx)" class="flex-none w-[100px] h-[100px] rounded-md overflow-hidden border-2" :class="idx === curIndex ? 'ring-2 ring-indigo-500' : 'ring-0'">
                  <img :src="img.url" :alt="img.alt" class="w-full h-full object-cover" />
                </button>
              </template>
            </div>
          </div>
        </div>

        <!-- Content: Title, Owner, Specs, Features, Description, Reviews -->
        <div class="mt-6">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Sonalika DI 745 III Tractor - Premium Agricultural Equipment</h1>

          <!-- Owner Card -->
          <div class="mt-4 flex items-center space-x-3">
            <img src="https://i.pravatar.cc/48?u=tilahun" alt="Tilahun Hailu" class="w-12 h-12 rounded-full object-cover" />
            <div>
              <div class="font-medium">Tilahun Hailu <span class="text-sm text-yellow-500 font-semibold ml-2">GOLD</span></div>
              <div class="text-sm text-gray-500">4.8★ · 34 reviews</div>
            </div>
            <div class="ml-auto">
              <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm">Contact owner</button>
            </div>
          </div>

          <!-- Specs + Features -->
          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded-lg shadow">
              <h3 class="text-lg font-semibold mb-3">Specifications</h3>
              <table class="w-full text-sm text-gray-700">
                <tbody>
                  <tr class="border-b"><th class="text-left py-2">Brand</th><td class="py-2">Sonalika</td></tr>
                  <tr class="border-b"><th class="text-left py-2">Model</th><td class="py-2">DI 745 III</td></tr>
                  <tr class="border-b"><th class="text-left py-2">Year</th><td class="py-2">2020</td></tr>
                  <tr class="border-b"><th class="text-left py-2">Power</th><td class="py-2">50 HP</td></tr>
                  <tr class="border-b"><th class="text-left py-2">Fuel</th><td class="py-2">Diesel</td></tr>
                  <tr class="border-b"><th class="text-left py-2">Transmission</th><td class="py-2">Manual</td></tr>
                </tbody>
              </table>
            </div>

            <div class="bg-white p-4 rounded-lg shadow">
              <h3 class="text-lg font-semibold mb-3">Features</h3>
              <div class="flex flex-wrap gap-2">
                <template x-for="feature in features" :key="feature">
                  <span class="px-3 py-1 bg-gray-100 text-sm rounded-full text-gray-800"> <span x-text="feature"></span> </span>
                </template>
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="mt-6 bg-white p-4 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-2">Description</h3>
            <p class="text-gray-700">Well-maintained agricultural tractor, perfect for commercial farming. Recently serviced and ready for heavy-duty tasks. Ideal for plowing, hauling, and field prep. Includes power steering, hydraulic lift, and a PTO shaft for implements.</p>
          </div>

          <!-- Reviews -->
          <div class="mt-6 bg-white p-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
              <div>
                <div class="text-2xl font-bold">4.8</div>
                <div class="text-sm text-gray-500">Aggregate rating · 34 reviews</div>
              </div>
              <div>
                <a href="#reviews" class="text-sm text-indigo-600">Write a review</a>
              </div>
            </div>

            <div class="mt-4 space-y-4" id="reviews">
              <template x-for="(r, idx) in visibleReviews" :key="idx">
                <div class="border p-3 rounded">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                      <img :src="r.avatar" class="w-10 h-10 rounded-full" />
                      <div>
                        <div class="font-medium" x-text="r.author"></div>
                        <div class="text-sm text-gray-500" x-text="r.date"></div>
                      </div>
                    </div>
                    <div class="text-sm text-yellow-400 font-semibold" x-text="r.rating + '★'"></div>
                  </div>
                  <p class="mt-2 text-gray-700" x-text="r.body"></p>
                </div>
              </template>

              <div class="text-center">
                <button x-show="!allReviewsShown" @click="loadMoreReviews" class="text-indigo-600">Load more reviews</button>
                <button x-show="allReviewsShown" @click="collapseReviews" class="text-indigo-600">Show less</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Pricing card (col-span 5) -->
      <aside class="lg:col-span-5">
        <div class="lg:sticky top-20 space-y-4">
          <div class="bg-white p-6 rounded-lg shadow">
            <div class="text-sm text-gray-500">Pricing</div>
            <div class="mt-2 text-3xl font-extrabold">65 ETB <span class="text-base font-medium text-gray-500">/ hr</span></div>

            <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
              <div class="py-2">Daily</div><div class="font-semibold">450 ETB</div>
              <div class="py-2">Weekly</div><div class="font-semibold">2,600 ETB</div>
              <div class="py-2">Monthly</div><div class="font-semibold">8,500 ETB</div>
              <div class="py-2">Security deposit</div><div class="font-semibold">5,000 ETB</div>
            </div>

            <button class="w-full mt-6 bg-indigo-600 text-white py-3 rounded-lg text-lg font-semibold">Book now</button>

            <!-- Availability -->
            <div class="mt-4">
              <div class="text-sm font-medium">Availability</div>
              <div class="text-sm text-gray-600 mt-1">May 20 - June 30, 2026</div>
              <div class="mt-2 text-sm">
                <template x-for="b in booked" :key="b.start">
                  <div class="text-sm text-red-600">Booked: <span x-text="b.start"></span> — <span x-text="b.end"></span></div>
                </template>
              </div>
            </div>

            <!-- Handoff -->
            <div class="mt-4 inline-flex items-center gap-2 bg-gray-100 px-3 py-2 rounded-full text-sm">
              <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0-1.657 0-5 0-5s3 0 3 3-1 2-1 2-2 0-2 0z"/></svg>
              <div class="text-gray-800 font-medium">QR-based digital handoff</div>
            </div>

            <!-- Owner buttons -->
            <div class="mt-4 flex gap-2">
              <button class="flex-1 border border-gray-300 px-3 py-2 rounded">Contact owner</button>
              <a href="#" class="flex-1 text-center bg-white border border-gray-300 px-3 py-2 rounded">View profile</a>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </section>

  <!-- Lightbox -->
  <div x-show="lightbox" x-transition class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center" style="display:none;">
    <div class="relative max-w-4xl w-full">
      <button @click="closeLightbox" class="absolute right-2 top-2 bg-white p-2 rounded">Close</button>
      <img :src="images[lightboxIndex].url" class="w-full h-[600px] object-contain rounded" />
    </div>
  </div>

  <!-- Related assets carousel -->
  <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h2 class="text-lg font-semibold mb-4">Related assets</h2>
    <div class="bg-white p-4 rounded shadow">
      <div class="flex gap-4 overflow-x-auto">
        <template x-for="(r, i) in related" :key="i">
          <div class="w-64 flex-none">
            <img :src="r.image" class="w-full h-40 object-cover rounded" />
            <div class="mt-2 text-sm font-medium" x-text="r.title"></div>
            <div class="text-sm text-gray-500" x-text="r.price"></div>
          </div>
        </template>
      </div>
    </div>
  </footer>

</div>

@push('scripts')
<script>
  function assetDetail(){
    return {
      images: [
        {url: '/images/tractor-1.jpg', alt: 'Tractor front view'},
        {url: '/images/tractor-2.jpg', alt: 'Tractor side view'},
        {url: '/images/tractor-3.jpg', alt: 'Tractor cabin'},
        {url: '/images/tractor-4.jpg', alt: 'PTO shaft'},
        {url: '/images/tractor-5.jpg', alt: 'Hydraulic system'},
        {url: '/images/tractor-6.jpg', alt: 'Field work'},
        {url: '/images/tractor-7.jpg', alt: 'Rear view'},
        {url: '/images/tractor-8.jpg', alt: 'Close-up'},
      ],
      curIndex: 0,
      favorite: false,
      lightbox: false,
      lightboxIndex: 0,
      features: ['Power steering','Hydraulic system','PTO shaft','Recent service'],
      reviews: [
        {author: 'Amanuel K.', avatar: 'https://i.pravatar.cc/48?u=amanuel', rating: 5, date: 'May 10, 2026', body: 'Great tractor, worked well for our farm.'},
        {author: 'Selam T.', avatar: 'https://i.pravatar.cc/48?u=selam', rating: 4.5, date: 'Apr 20, 2026', body: 'Well maintained and clean. Smooth handoff.'},
        {author: 'Bekele D.', avatar: 'https://i.pravatar.cc/48?u=bekele', rating: 5, date: 'Mar 15, 2026', body: 'Powerful and reliable for heavy tasks.'},
        {author: 'Additional 1', avatar: 'https://i.pravatar.cc/48?u=a1', rating: 4, date: 'Feb 02, 2026', body: 'Good overall.'},
        {author: 'Additional 2', avatar: 'https://i.pravatar.cc/48?u=a2', rating: 4, date: 'Jan 18, 2026', body: 'No issues.'}
      ],
      allReviewsShown: false,
      related: [
        {image: '/images/related-1.jpg', title: 'Cultivator - Medium', price: '120 ETB / day'},
        {image: '/images/related-2.jpg', title: 'Trailer - Heavy Duty', price: '300 ETB / day'},
        {image: '/images/related-3.jpg', title: 'Seeder', price: '90 ETB / day'},
      ],
      booked: [
        {start: 'May 25, 2026', end: 'May 26, 2026'},
        {start: 'June 5, 2026', end: 'June 7, 2026'}
      ],
      prev(){ this.curIndex = (this.curIndex - 1 + this.images.length) % this.images.length },
      next(){ this.curIndex = (this.curIndex + 1) % this.images.length },
      goTo(i){ this.curIndex = i },
      openLightbox(i){ this.lightboxIndex = i; this.lightbox = true },
      closeLightbox(){ this.lightbox = false },
      toggleFavorite(){ this.favorite = !this.favorite },
      get visibleReviews(){ return this.allReviewsShown ? this.reviews : this.reviews.slice(0,3) },
      loadMoreReviews(){ this.allReviewsShown = true },
      collapseReviews(){ this.allReviewsShown = false }
    }
  }
</script>
@endpush

@endsection
