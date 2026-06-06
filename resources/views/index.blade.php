@extends('layouts.app')

@section('content')
<div x-data="dashboardPage()" x-init="init()" class="min-h-screen bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
            <div class="min-w-0">
                <p class="text-sm font-medium uppercase tracking-[0.24em] text-emerald-600">Ethio-Share</p>
                <h1 class="mt-1 truncate text-2xl font-semibold text-slate-900 sm:text-3xl">Welcome back, Abebe</h1>
                <p class="mt-1 text-sm text-slate-500">Manage your profile, bookings, assets, and reputation from one place.</p>
            </div>

            <div class="relative shrink-0">
                <button type="button" @click="profileMenuOpen = !profileMenuOpen" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-semibold text-white shadow-lg shadow-emerald-500/20">AT</span>
                    <span class="hidden sm:block">
                        <span class="block text-sm font-semibold text-slate-900">Abebe Tekle</span>
                        <span class="block text-xs text-slate-500">GOLD tier · Oromia</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                </button>

                <div x-show="profileMenuOpen" @click.outside="profileMenuOpen = false" x-transition.opacity.scale.origin.top.right class="absolute right-0 mt-3 w-72 rounded-3xl border border-slate-200 bg-white p-3 shadow-2xl shadow-slate-900/10" style="display:none;">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center gap-3">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-sm font-semibold text-white">AT</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Abebe Tekle</p>
                                <p class="text-xs text-slate-500">abebe@example.et</p>
                                <p class="text-xs text-slate-500">+251911234567</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 grid gap-2 text-sm">
                        <a href="#" class="rounded-2xl px-3 py-2 text-slate-700 transition hover:bg-slate-100">View public profile</a>
                        <a href="#" class="rounded-2xl px-3 py-2 text-slate-700 transition hover:bg-slate-100">Account settings</a>
                        <a href="#" class="rounded-2xl px-3 py-2 text-slate-700 transition hover:bg-slate-100">Sign out</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-3 shadow-sm shadow-slate-900/5 sm:p-4">
            <div class="flex flex-wrap gap-2">
                <template x-for="tab in tabs" :key="tab.key">
                    <button type="button" @click="switchTab(tab.key)" class="inline-flex items-center gap-2 rounded-2xl px-4 py-2.5 text-sm font-semibold transition" :class="activeTab === tab.key ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                        <i class="fa-solid" :class="tab.icon"></i>
                        <span x-text="tab.label"></span>
                    </button>
                </template>
            </div>
        </section>

        <section x-show="activeTab === 'overview'" x-transition.opacity class="mt-6 space-y-6">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                <template x-for="stat in overviewStats" :key="stat.label">
                    <article class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-medium text-slate-500" x-text="stat.label"></p>
                                <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900" x-text="stat.value"></p>
                                <p class="mt-1 text-sm text-slate-500" x-text="stat.subtitle"></p>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">
                                <i class="fa-solid" :class="stat.icon"></i>
                            </div>
                        </div>
                    </article>
                </template>
            </div>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.6fr)_minmax(0,0.9fr)]">
                <article class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Recent activity</h2>
                            <p class="mt-1 text-sm text-slate-500">Latest bookings, transactions, and reputation events.</p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">8 items</span>
                    </div>

                    <div class="mt-5 space-y-4">
                        <template x-for="activity in recentActivity" :key="activity.title + activity.date">
                            <div class="flex gap-4 rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl" :class="activity.badgeClass">
                                    <i class="fa-solid" :class="activity.icon"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-sm font-semibold text-slate-900" x-text="activity.title"></h3>
                                        <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500" x-text="activity.meta"></span>
                                    </div>
                                    <p class="mt-1 text-sm text-slate-600" x-text="activity.description"></p>
                                    <p class="mt-2 text-xs font-medium text-slate-400" x-text="activity.date"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </article>

                <aside class="space-y-6">
                    <article class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 p-5 text-white shadow-sm shadow-slate-900/10">
                        <p class="text-sm text-slate-300">Quick actions</p>
                        <h2 class="mt-2 text-xl font-semibold">Move faster</h2>
                        <p class="mt-2 text-sm text-slate-300">Continue with the most common account actions.</p>
                        <div class="mt-5 grid gap-3">
                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-emerald-400">
                                <i class="fa-solid fa-calendar-plus"></i>
                                <span>Book an asset</span>
                            </button>
                            <button type="button" x-show="isOwner" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15" style="display:none;">
                                <i class="fa-solid fa-circle-plus"></i>
                                <span>List asset</span>
                            </button>
                            <button type="button" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/15">
                                <i class="fa-solid fa-user"></i>
                                <span>View profile</span>
                            </button>
                        </div>
                    </article>

                    <article class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Profile summary</h2>
                                <p class="text-sm text-slate-500">Abebe Tekle · Oromia</p>
                            </div>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">GOLD</span>
                        </div>
                        <dl class="mt-5 grid grid-cols-2 gap-4 text-sm">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-slate-500">Email</dt>
                                <dd class="mt-1 font-semibold text-slate-900">abebe@example.et</dd>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-slate-500">Phone</dt>
                                <dd class="mt-1 font-semibold text-slate-900">+251911234567</dd>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-slate-500">Trust score</dt>
                                <dd class="mt-1 font-semibold text-slate-900">82.3</dd>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <dt class="text-slate-500">Region</dt>
                                <dd class="mt-1 font-semibold text-slate-900">Oromia</dd>
                            </div>
                        </dl>
                    </article>
                </aside>
            </div>
        </section>

        <section x-show="activeTab === 'bookings'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">My Bookings</h2>
                    <p class="mt-1 text-sm text-slate-500">Review current, completed, and cancelled bookings.</p>
                </div>

                <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm shadow-slate-900/5">
                    <template x-for="filter in bookingFilters" :key="filter.key">
                        <button type="button" @click="bookingFilter = filter.key" class="rounded-2xl px-4 py-2 text-sm font-semibold transition" :class="bookingFilter === filter.key ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100'" x-text="filter.label"></button>
                    </template>
                </div>
            </div>

            <div x-show="filteredBookings.length" class="grid gap-4 md:grid-cols-2">
                <template x-for="booking in filteredBookings" :key="booking.id">
                    <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm shadow-slate-900/5">
                        <div class="grid grid-cols-[112px_minmax(0,1fr)] gap-4 p-4 sm:grid-cols-[144px_minmax(0,1fr)]">
                            <img :src="booking.image" :alt="booking.asset" class="h-28 w-full rounded-2xl object-cover sm:h-32" />
                            <div class="min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <h3 class="truncate text-lg font-semibold text-slate-900" x-text="booking.asset"></h3>
                                        <p class="mt-1 text-sm text-slate-500">Owner: <span x-text="booking.owner"></span></p>
                                    </div>
                                    <span class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold" :class="booking.badgeClass" x-text="booking.status"></span>
                                </div>

                                <div class="mt-4 space-y-2 text-sm text-slate-600">
                                    <p><span class="font-medium text-slate-900">Dates:</span> <span x-text="booking.dates"></span></p>
                                    <p><span class="font-medium text-slate-900">Total:</span> <span x-text="booking.amount"></span></p>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View details</button>
                                    <button type="button" x-show="booking.canCancel" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100">Cancel</button>
                                    <button type="button" x-show="booking.canReview" class="rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">Leave review</button>
                                </div>
                            </div>
                        </div>
                    </article>
                </template>
            </div>

            <div x-show="!filteredBookings.length" class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-sm text-slate-500">
                No bookings yet.
            </div>
        </section>

        <section x-show="activeTab === 'assets'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">My Assets</h2>
                    <p class="mt-1 text-sm text-slate-500">Manage your listings, earnings, and performance.</p>
                </div>

                <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>List new asset</span>
                </button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <template x-for="asset in assets" :key="asset.title">
                    <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm shadow-slate-900/5">
                        <div class="grid grid-cols-[120px_minmax(0,1fr)] gap-4 p-4 sm:grid-cols-[156px_minmax(0,1fr)]">
                            <img :src="asset.image" :alt="asset.title" class="h-28 w-full rounded-2xl object-cover sm:h-32" />
                            <div>
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900" x-text="asset.title"></h3>
                                        <p class="mt-1 text-sm text-slate-500">Last booked <span x-text="asset.lastBooked"></span></p>
                                    </div>
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700" x-text="asset.status"></span>
                                </div>

                                <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                                    <p class="text-sm text-slate-500">Total earnings this month</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900" x-text="asset.earnings"></p>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Edit</button>
                                    <button type="button" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">Delist</button>
                                    <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View analytics</button>
                                </div>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </section>

        <section x-show="activeTab === 'transactions'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Transactions</h2>
                    <p class="mt-1 text-sm text-slate-500">Escrow status and payment history.</p>
                </div>

                <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm shadow-slate-900/5">
                    <template x-for="filter in transactionFilters" :key="filter.key">
                        <button type="button" @click="transactionFilter = filter.key" class="rounded-2xl px-4 py-2 text-sm font-semibold transition" :class="transactionFilter === filter.key ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100'" x-text="filter.label"></button>
                    </template>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm shadow-slate-900/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">Asset</th>
                                <th class="px-6 py-4 font-semibold">Amount</th>
                                <th class="px-6 py-4 font-semibold">Status</th>
                                <th class="px-6 py-4 font-semibold">Escrow Status</th>
                                <th class="px-6 py-4 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="transaction in filteredTransactions" :key="transaction.id">
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-6 py-4 text-slate-700" x-text="transaction.date"></td>
                                    <td class="px-6 py-4 font-medium text-slate-900" x-text="transaction.asset"></td>
                                    <td class="px-6 py-4 text-slate-700" x-text="transaction.amount"></td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="transaction.statusClass" x-text="transaction.status"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="transaction.escrowClass" x-text="transaction.escrow"></span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between gap-4 border-t border-slate-200 px-6 py-4 text-sm text-slate-500">
                    <p>Showing 1-4 of 4 transactions</p>
                    <div class="flex items-center gap-2">
                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 font-semibold text-slate-700 transition hover:bg-slate-50">Previous</button>
                        <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 font-semibold text-slate-700 transition hover:bg-slate-50">Next</button>
                    </div>
                </div>
            </div>
        </section>

        <section x-show="activeTab === 'disputes'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Disputes</h2>
                    <p class="mt-1 text-sm text-slate-500">Track open and resolved cases.</p>
                </div>

                <div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-2 shadow-sm shadow-slate-900/5">
                    <template x-for="filter in disputeFilters" :key="filter.key">
                        <button type="button" @click="disputeFilter = filter.key" class="rounded-2xl px-4 py-2 text-sm font-semibold transition" :class="disputeFilter === filter.key ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100'" x-text="filter.label"></button>
                    </template>
                </div>
            </div>

            <div x-show="filteredDisputes.length" class="grid gap-4">
                <template x-for="dispute in filteredDisputes" :key="dispute.title + dispute.updatedAt">
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900" x-text="dispute.title"></h3>
                                <p class="mt-1 text-sm text-slate-500">Initiator: <span x-text="dispute.initiator"></span></p>
                                <p class="mt-2 text-sm text-slate-600">Last updated: <span x-text="dispute.updatedAt"></span></p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="dispute.badgeClass" x-text="dispute.status"></span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">View details</button>
                            <button type="button" x-show="dispute.canAddEvidence" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">Add evidence</button>
                        </div>
                    </article>
                </template>
            </div>

            <div x-show="!filteredDisputes.length" class="rounded-[2rem] border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-sm text-slate-500">
                No disputes.
            </div>
        </section>

        <section x-show="activeTab === 'reviews'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Reviews</h2>
                <p class="mt-1 text-sm text-slate-500">Feedback from recent renters and collaborators.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <template x-for="review in reviews" :key="review.author + review.date">
                    <article class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                        <div class="flex items-start gap-4">
                            <img :src="review.avatar" :alt="review.author" class="h-12 w-12 rounded-2xl object-cover" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900" x-text="review.author"></h3>
                                        <p class="text-xs text-slate-500" x-text="review.date"></p>
                                    </div>
                                    <div class="flex items-center gap-1 text-amber-500">
                                        <template x-for="star in 5" :key="star">
                                            <i class="fa-solid fa-star text-xs" :class="star <= review.rating ? 'opacity-100' : 'opacity-20'"></i>
                                        </template>
                                    </div>
                                </div>
                                <p class="mt-4 text-sm leading-6 text-slate-600" x-text="review.comment"></p>
                                <button type="button" class="mt-4 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Helpful</button>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </section>

        <section x-show="activeTab === 'favorites'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Favorites</h2>
                    <p class="mt-1 text-sm text-slate-500">Saved assets you may want to book again.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600" x-text="favoriteAssets.length + ' saved assets'"></span>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <template x-for="favorite in favoriteAssets" :key="favorite.title">
                    <article class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm shadow-slate-900/5">
                        <img :src="favorite.image" :alt="favorite.title" class="h-48 w-full object-cover" />
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-900" x-text="favorite.title"></h3>
                                    <p class="mt-1 text-sm text-slate-500" x-text="favorite.owner"></p>
                                </div>
                                <button type="button" class="rounded-full bg-rose-50 p-2 text-rose-600 transition hover:bg-rose-100">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                            </div>
                            <p class="mt-4 text-sm text-slate-600" x-text="favorite.summary"></p>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="font-semibold text-slate-900" x-text="favorite.price"></span>
                                <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 font-semibold text-slate-700 transition hover:bg-slate-50">Book again</button>
                            </div>
                        </div>
                    </article>
                </template>
            </div>
        </section>

        <section x-show="activeTab === 'settings'" x-transition.opacity class="mt-6 space-y-6" style="display:none;">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">Settings</h2>
                <p class="mt-1 text-sm text-slate-500">Update your profile, password, notifications, and privacy preferences.</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <form class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                    <h3 class="text-lg font-semibold text-slate-900">Profile</h3>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Full name</span>
                            <input type="text" value="Abebe Tekle" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-0 transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />
                        </label>
                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Region</span>
                            <input type="text" value="Oromia" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-0 transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Email</span>
                            <input type="email" value="abebe@example.et" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-0 transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />
                        </label>
                        <label class="block sm:col-span-2">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Phone</span>
                            <input type="tel" value="+251911234567" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-0 transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />
                        </label>
                    </div>
                    <div class="mt-5">
                        <button type="button" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500">Save profile</button>
                    </div>
                </form>

                <div class="space-y-6">
                    <form class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                        <h3 class="text-lg font-semibold text-slate-900">Password</h3>
                        <div class="mt-5 grid gap-4">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Current password</span>
                                <input type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-0 transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">New password</span>
                                <input type="password" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none ring-0 transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" />
                            </label>
                        </div>
                        <div class="mt-5">
                            <button type="button" class="rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Change password</button>
                        </div>
                    </form>

                    <form class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                        <h3 class="text-lg font-semibold text-slate-900">Preferences</h3>
                        <div class="mt-5 space-y-4 text-sm">
                            <label class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                <span>
                                    <span class="block font-medium text-slate-900">Booking notifications</span>
                                    <span class="block text-slate-500">Receive alerts for booking updates.</span>
                                </span>
                                <input type="checkbox" checked class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                            </label>
                            <label class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                <span>
                                    <span class="block font-medium text-slate-900">Privacy mode</span>
                                    <span class="block text-slate-500">Hide contact details from public view.</span>
                                </span>
                                <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                            </label>
                            <label class="flex items-center justify-between gap-4 rounded-2xl bg-slate-50 px-4 py-3">
                                <span>
                                    <span class="block font-medium text-slate-900">Marketing emails</span>
                                    <span class="block text-slate-500">Get platform tips and promotions.</span>
                                </span>
                                <input type="checkbox" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                            </label>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>

@push('scripts')
<script>
    function dashboardPage() {
        return {
            activeTab: 'overview',
            profileMenuOpen: false,
            isOwner: true,
            bookingFilter: 'active',
            transactionFilter: 'pending',
            disputeFilter: 'open',
            tabs: [
                { key: 'overview', label: 'Overview', icon: 'fa-house' },
                { key: 'bookings', label: 'My Bookings', icon: 'fa-calendar-check' },
                { key: 'assets', label: 'My Assets', icon: 'fa-box-archive' },
                { key: 'transactions', label: 'Transactions', icon: 'fa-arrow-right-arrow-left' },
                { key: 'disputes', label: 'Disputes', icon: 'fa-scale-balanced' },
                { key: 'reviews', label: 'Reviews', icon: 'fa-star' },
                { key: 'favorites', label: 'Favorites', icon: 'fa-heart' },
                { key: 'settings', label: 'Settings', icon: 'fa-gear' },
            ],
            bookingFilters: [
                { key: 'active', label: 'Active' },
                { key: 'completed', label: 'Completed' },
                { key: 'cancelled', label: 'Cancelled' },
            ],
            transactionFilters: [
                { key: 'pending', label: 'Pending' },
                { key: 'completed', label: 'Completed' },
                { key: 'failed', label: 'Failed' },
            ],
            disputeFilters: [
                { key: 'open', label: 'Open' },
                { key: 'resolved', label: 'Resolved' },
            ],
            stats: {
                totalBookings: 34,
                trustScore: 82.3,
                earnings: 'ETB 45,230',
                activeDisputes: 0,
            },
            overviewStats: [],
            recentActivity: [],
            bookings: [],
            assets: [],
            favoriteAssets: [],
            transactions: [],
            disputes: [],
            reviews: [],
            init() {
                this.overviewStats = [
                    { label: 'Total bookings', value: '34', subtitle: 'completed', icon: 'fa-calendar-check' },
                    { label: 'Trust score', value: '78.5', subtitle: 'GOLD tier', icon: 'fa-shield-heart' },
                    { label: 'Earnings', value: 'ETB 45,230', subtitle: 'this month', icon: 'fa-sack-dollar' },
                    { label: 'Active disputes', value: '0', subtitle: 'open', icon: 'fa-scale-balanced' },
                ];

                this.recentActivity = [
                    { title: 'Booking completed', meta: 'Booking', description: 'Sonalika Tractor finished successfully.', date: 'May 20, 2026', icon: 'fa-check-circle', badgeClass: 'bg-emerald-100 text-emerald-700' },
                    { title: 'Escrow released', meta: 'Transaction', description: 'ETB 2,500 moved from escrow to the owner.', date: 'May 21, 2026', icon: 'fa-money-bill-transfer', badgeClass: 'bg-amber-100 text-amber-700' },
                    { title: 'Review submitted', meta: 'Review', description: 'A renter left a 5-star review after a smooth handoff.', date: 'May 21, 2026', icon: 'fa-star', badgeClass: 'bg-amber-100 text-amber-700' },
                    { title: 'KYC upgraded to GOLD', meta: 'Trust', description: 'Verification review completed and trust tier increased.', date: 'May 10, 2026', icon: 'fa-shield-heart', badgeClass: 'bg-sky-100 text-sky-700' },
                    { title: 'Active booking started', meta: 'Booking', description: 'Excavator rental begins for a new construction site.', date: 'May 25, 2026', icon: 'fa-truck-ramp-box', badgeClass: 'bg-indigo-100 text-indigo-700' },
                    { title: 'Asset listed', meta: 'Asset', description: 'JCB Excavator published and made available for rental.', date: 'May 4, 2026', icon: 'fa-box-archive', badgeClass: 'bg-emerald-100 text-emerald-700' },
                    { title: 'Profile updated', meta: 'Profile', description: 'Phone number and regional details were refreshed.', date: 'May 2, 2026', icon: 'fa-user-pen', badgeClass: 'bg-slate-200 text-slate-700' },
                    { title: 'Payout settled', meta: 'Transaction', description: 'A weekly payout was deposited successfully.', date: 'Apr 30, 2026', icon: 'fa-circle-check', badgeClass: 'bg-emerald-100 text-emerald-700' },
                ];

                this.bookings = [
                    { id: 1, filter: 'active', asset: 'Excavator rental', owner: 'Tilahun Hailu', dates: 'May 25-27, 2026', status: 'Pending', amount: 'ETB 12,400', image: 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=800&q=80', badgeClass: 'bg-amber-50 text-amber-700', canCancel: true, canReview: false },
                    { id: 2, filter: 'active', asset: 'Tractor rental', owner: 'Mulugeta A.', dates: 'May 28-30, 2026', status: 'Confirmed', amount: 'ETB 8,900', image: 'https://images.unsplash.com/photo-1598529329163-1a35f4f4f6f5?auto=format&fit=crop&w=800&q=80', badgeClass: 'bg-emerald-50 text-emerald-700', canCancel: false, canReview: false },
                    { id: 3, filter: 'completed', asset: 'Sonalika Tractor', owner: 'Tilahun Hailu', dates: 'May 20-22, 2026', status: 'Completed', amount: 'ETB 9,600', image: 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=800&q=80', badgeClass: 'bg-emerald-50 text-emerald-700', canCancel: false, canReview: true },
                    { id: 4, filter: 'cancelled', asset: 'Harvest trailer', owner: 'Abel K.', dates: 'Apr 15-17, 2026', status: 'Cancelled', amount: 'ETB 3,200', image: 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=800&q=80', badgeClass: 'bg-rose-50 text-rose-700', canCancel: false, canReview: false },
                ];

                this.assets = [
                    { title: 'Sonalika Tractor', status: 'Active', earnings: 'ETB 45,230', lastBooked: 'May 21, 2026', image: 'https://images.unsplash.com/photo-1598515214211-89d3c56f8f86?auto=format&fit=crop&w=800&q=80' },
                    { title: 'JCB Excavator', status: 'Active', earnings: 'ETB 32,100', lastBooked: 'May 18, 2026', image: 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=800&q=80' },
                ];

                this.transactions = [
                    { id: 1, filter: 'pending', date: 'May 21, 2026', asset: 'Excavator rental', amount: 'ETB 12,400', status: 'Pending', escrow: 'Held', statusClass: 'bg-amber-50 text-amber-700', escrowClass: 'bg-blue-50 text-blue-700' },
                    { id: 2, filter: 'completed', date: 'May 20, 2026', asset: 'Sonalika Tractor', amount: 'ETB 9,600', status: 'Completed', escrow: 'Released', statusClass: 'bg-emerald-50 text-emerald-700', escrowClass: 'bg-emerald-50 text-emerald-700' },
                    { id: 3, filter: 'completed', date: 'May 10, 2026', asset: 'Trailer rental', amount: 'ETB 4,800', status: 'Completed', escrow: 'Released', statusClass: 'bg-emerald-50 text-emerald-700', escrowClass: 'bg-emerald-50 text-emerald-700' },
                    { id: 4, filter: 'failed', date: 'Apr 29, 2026', asset: 'Seeder booking', amount: 'ETB 2,100', status: 'Failed', escrow: 'Refunded', statusClass: 'bg-rose-50 text-rose-700', escrowClass: 'bg-rose-50 text-rose-700' },
                ];

                this.disputes = [
                    { title: 'Delayed handoff for excavator rental', initiator: 'Abebe Tekle', status: 'Open', updatedAt: 'May 22, 2026', filter: 'open', badgeClass: 'bg-rose-50 text-rose-700', canAddEvidence: true },
                    { title: 'Fuel reimbursement review', initiator: 'Tilahun Hailu', status: 'Resolved', updatedAt: 'May 12, 2026', filter: 'resolved', badgeClass: 'bg-emerald-50 text-emerald-700', canAddEvidence: false },
                ];

                this.reviews = [
                    { author: 'Sewit A.', avatar: 'https://i.pravatar.cc/120?img=32', rating: 5, comment: 'The booking process was clear and the asset was delivered on time.', date: 'May 22, 2026' },
                    { author: 'Hana T.', avatar: 'https://i.pravatar.cc/120?img=47', rating: 5, comment: 'Excellent communication and very trustworthy owner.', date: 'May 18, 2026' },
                    { author: 'Mekdes G.', avatar: 'https://i.pravatar.cc/120?img=12', rating: 4, comment: 'Overall smooth experience. Handoff and support were reliable.', date: 'May 15, 2026' },
                    { author: 'Kebede F.', avatar: 'https://i.pravatar.cc/120?img=65', rating: 5, comment: 'Great service and the asset performed as expected.', date: 'May 10, 2026' },
                    { author: 'Nigus B.', avatar: 'https://i.pravatar.cc/120?img=19', rating: 5, comment: 'Would book again. The owner responded fast and clearly.', date: 'May 7, 2026' },
                    { author: 'Rahel M.', avatar: 'https://i.pravatar.cc/120?img=24', rating: 4, comment: 'Good value and well maintained equipment.', date: 'Apr 28, 2026' },
                ];

                this.favoriteAssets = [
                    { title: 'Sonalika Tractor', owner: 'Tilahun Hailu', summary: 'Reliable for farm preparation, hauling, and seasonal work.', price: 'From ETB 2,800/day', image: 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=800&q=80' },
                    { title: 'JCB Excavator', owner: 'Mulugeta A.', summary: 'Heavy-duty earthmoving with strong availability in Oromia.', price: 'From ETB 6,400/day', image: 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=800&q=80' },
                    { title: 'Harvest Trailer', owner: 'Abel K.', summary: 'Useful for transport, crop movement, and material delivery.', price: 'From ETB 1,200/day', image: 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=800&q=80' },
                ];
            },
            switchTab(tab) {
                this.activeTab = tab;
                this.profileMenuOpen = false;

                if (tab === 'bookings' && !this.bookings.length) {
                    this.fetchBookings();
                }

                if (tab === 'assets' && !this.assets.length) {
                    this.fetchAssets();
                }
            },
            get filteredBookings() {
                return this.bookings.filter((booking) => booking.filter === this.bookingFilter);
            },
            get filteredTransactions() {
                return this.transactions.filter((transaction) => transaction.filter === this.transactionFilter);
            },
            get filteredDisputes() {
                return this.disputes.filter((dispute) => dispute.filter === this.disputeFilter);
            },
            fetchBookings() {},
            fetchAssets() {},
        };
    }
</script>
@endpush
@endsection