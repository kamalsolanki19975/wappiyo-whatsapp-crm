<template>
    <div>
        <section id="section1" class="pt-2 md:pt-3 px-5 md:px-10 lg:px-20 2xl:px-60 relative" style="color:white;background: rgb(16 65 23 / 66%);padding-bottom:52px;">
            <video autoplay muted loop playsinline class="absolute top-0 left-0 w-full h-full object-cover z-[-1]">
                <source :src="'/WhatsAppVideo.mp4'" type="video/mp4">
            </video>
            <div class="flex justify-between items-center">
                <Link href="/" class="inline-block">
                    <img class="max-w-[150px]" v-if="props.companyConfig.logo" :src="'/media/' + props.companyConfig.logo" :alt="props.companyConfig.company_name">
                    <h1 v-else class="text-2xl">{{ props.companyConfig.company_name }}</h1>
                </Link>
                <div class="hidden lg:flex justify-center items-center text-md space-x-8">
                    <a href="#section2" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Features') }}</a>
                    <a href="#section4" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Pricing') }}</a>
                    <a href="#section5" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Reviews') }}</a>
                    <a href="#section6" class="cursor-pointer hover:border-b-2 border-black">{{ $t('FAQs') }}</a>
                    <a href="#section7" class="cursor-pointer hover:border-b-2 border-black">{{ $t('Contact us') }}</a>
                </div>
                
                <div class="flex justify-end items-center text-md">
                    <Link href="/login" class="hidden md:inline-block mr-10 cursor-pointer hover:text-gray-400 border-black">{{ $t('Login') }}</Link>
                    <Link href="/signup" class="hidden md:inline-block bg-primary hover:bg-secondary text-white p-2 rounded-lg text-sm w-fit px-8">{{ $t('Sign Up') }}</Link>
                    <Link href="/login" class="md:hidden bg-primary hover:bg-secondary text-white p-2 rounded-lg text-sm flex w-fit px-8">{{ $t('Login') }}</Link>
                </div>
            </div>
            <div class="flex justify-center mt-[7%] h-full">
                <div class="relative z-10">
                    <div class="flex justify-center mb-6">
                        <div class="bg-secondary/10 text-primary text-sm py-2 px-4 inline-block rounded-full mb-6 aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
                            <a href="#">
                                <div class="flex items-center gap-2">
                                    <div class="inline-block px-2 text-sm text-white rounded-full">🚀</div>
                                    <div class="text-white">{{ $t('Upgrade your messaging experience') }}</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <h1 class="text-center text-3xl md:text-3xl">{{ $t('Revolutionise Your Whatsapp Messaging Today') }}</h1>
                    </div>
                    <div class="flex justify-center">
                        <h2 class="text-center text-white font-light text-gray-700 text-md md:text-xl mt-8 w-3/4">
                            {{ $t("Transform the way you communicate on WhatsApp with our all-in-one solution designed for businesses and teams. Whether you're engaging with customers or collaborating with colleagues, we’ve got you covered!") }}.
                        </h2>                    
                    </div>
                    <div class="md:flex md:flex-row md:justify-center md:space-x-2 space-y-4 md:space-y-0 mt-10">
                       
                    </div>
                    <div class="flex flex-wrap justify-center items-center gap-5 mt-5 z-10">
                        
                    </div>
                </div>
            </div>  
        </section>
        <section id="section2" class="px-5 md:px-10 lg:px-20 py-20 2xl:px-60">
            <div class="flex justify-center mb-2">
                <div class="bg-secondary/10 text-secondary py-1 px-4 inline-block rounded-full mb-6 aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
                    <a href="#">
                        <div class="flex items-center gap-2">
                            <div>{{ $t('Features') }}</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="flex justify-center">
                <h2 class="text-center text-3xl md:text-3xl font-bold capitalize">{{ $t('Whatsapp messaging done differently') }}</h2>
            </div>
            <div class="flex justify-center">
                <h3 class="text-center text-[18px] mt-5 w-[33em]">{{ $t('Elevate your communication game and unlock a host of powerful features tailored to enhance your business\'s WhatsApp experience:') }}</h3>
            </div>
            <div class="bg-gray-300/10 rounded-[20px] px-5 md:px-10 py-10 grid lg:grid-cols-2 grid-cols-1 gap-6 items-center mt-14">
                <div class="">
                    <img :src="'images/hero/dashboard3.png'" alt="dashboard" data-aos="fade-right" data-aos-duration="400" class="aos-init aos-animate">
                </div>
                <div class="lg:ms-24">
                    <div @click="selectedFeature = 1" class="bg-white rounded-lg px-2 py-2 mb-5">
                        <div class="flex justify-between items-center cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="bg-secondary/10 rounded-lg flex items-center justify-center h-10 w-10">
                                    <svg class="h-6 w-6 text-secondary" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 26 26"><g fill="currentColor"><path d="M8.05 17.95a1 1 0 1 1 1.414-1.415A5 5 0 0 0 18 13a1 1 0 1 1 2 0a7 7 0 0 1-11.95 4.95"/><path d="M16.559 15.832a1 1 0 1 1-1.11-1.664l3-2a1 1 0 1 1 1.11 1.664z"/><path d="M21.832 15.445a1 1 0 0 1-1.664 1.11l-2-3a1 1 0 1 1 1.664-1.11zm-3.975-7.594a1 1 0 1 1-1.414 1.414a5 5 0 0 0-8.536 3.536a1 1 0 1 1-2 0a7 7 0 0 1 11.95-4.95"/><path d="M9.349 9.969a1 1 0 0 1 1.11 1.664l-3.001 2a1 1 0 1 1-1.11-1.664z"/><path d="M4.075 10.356a1 1 0 1 1 1.664-1.11l2 3a1 1 0 1 1-1.664 1.11z"/><path fill-rule="evenodd" d="M13 24c6.075 0 11-4.925 11-11S19.075 2 13 2S2 6.925 2 13s4.925 11 11 11m0 2c7.18 0 13-5.82 13-13S20.18 0 13 0S0 5.82 0 13s5.82 13 13 13" clip-rule="evenodd"/></g></svg>
                                </div>
                                <h4 class="font-regular text-gray-800">{{ $t('Instant connectivity') }}</h4>
                            </div>
                            <div>
                                <svg v-if="selectedFeature != 1" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M16.75 11.989a1.82 1.82 0 0 1-.57 1.36l-6.82 6.1a1.27 1.27 0 0 1-.65.31h-.19a1.3 1.3 0 0 1-.52-.1a1.23 1.23 0 0 1-.54-.47a1.19 1.19 0 0 1-.21-.68v-13a1.2 1.2 0 0 1 .21-.69a1.23 1.23 0 0 1 1.25-.56c.24.039.464.143.65.3l6.76 6.09c.19.162.344.363.45.59c.114.234.175.49.18.75"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M19.696 8.72a1.22 1.22 0 0 1-.3.64l-6.09 6.76a1.85 1.85 0 0 1-.58.46a1.7 1.7 0 0 1-1.42.03a1.75 1.75 0 0 1-.62-.42l-6.1-6.83a1.28 1.28 0 0 1-.31-.64a1.31 1.31 0 0 1 .56-1.26a1.36 1.36 0 0 1 .68-.21h13a1.293 1.293 0 0 1 1.15.76c.081.228.092.476.03.71"/></svg>
                            </div>
                        </div>
                        <div v-if="selectedFeature === 1" class="mt-2">
                            <p class="text-gray-700 dark:text-gray-300 font-light">
                                {{ $t('Engage with your audience in real-time through the WhatsApp Cloud API, ensuring swift and effective communication') }}.
                            </p>
                        </div>
                    </div>

                    <div  @click="selectedFeature = 2" class="bg-white rounded-lg px-2 py-2 mb-5">
                        <div class="flex justify-between items-center cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="bg-secondary/10 rounded-lg flex items-center justify-center h-10 w-10">
                                    <svg class="h-6 w-6 text-secondary" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="m8.85 12l1.45-1.45q.3-.3.288-.7t-.288-.7q-.3-.3-.712-.312t-.713.287L6.7 11.3q-.3.3-.3.7t.3.7l2.175 2.175q.3.3.713.288t.712-.313q.275-.3.288-.7t-.288-.7zm6.3 0l-1.45 1.45q-.3.3-.288.7t.288.7q.3.3.713.312t.712-.287L17.3 12.7q.3-.3.3-.7t-.3-.7l-2.175-2.175q-.3-.3-.712-.288t-.713.313q-.275.3-.287.7t.287.7zM5 21q-.825 0-1.412-.587T3 19V5q0-.825.588-1.412T5 3h4.2q.325-.9 1.088-1.45T12 1q.95 0 1.713.55T14.8 3H19q.825 0 1.413.588T21 5v14q0 .825-.587 1.413T19 21zm0-2h14V5H5zm7-14.75q.325 0 .538-.213t.212-.537q0-.325-.213-.537T12 2.75q-.325 0-.537.213t-.213.537q0 .325.213.538T12 4.25M5 19V5z"/></svg>
                                </div>
                                <h4 class="font-regular text-gray-800">{{ $t('Effortless integration') }}</h4>
                            </div>
                            <div>
                                <svg v-if="selectedFeature != 2" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M16.75 11.989a1.82 1.82 0 0 1-.57 1.36l-6.82 6.1a1.27 1.27 0 0 1-.65.31h-.19a1.3 1.3 0 0 1-.52-.1a1.23 1.23 0 0 1-.54-.47a1.19 1.19 0 0 1-.21-.68v-13a1.2 1.2 0 0 1 .21-.69a1.23 1.23 0 0 1 1.25-.56c.24.039.464.143.65.3l6.76 6.09c.19.162.344.363.45.59c.114.234.175.49.18.75"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M19.696 8.72a1.22 1.22 0 0 1-.3.64l-6.09 6.76a1.85 1.85 0 0 1-.58.46a1.7 1.7 0 0 1-1.42.03a1.75 1.75 0 0 1-.62-.42l-6.1-6.83a1.28 1.28 0 0 1-.31-.64a1.31 1.31 0 0 1 .56-1.26a1.36 1.36 0 0 1 .68-.21h13a1.293 1.293 0 0 1 1.15.76c.081.228.092.476.03.71"/></svg>
                            </div>
                        </div>
                        <div v-if="selectedFeature === 2" class="mt-2">
                            <p class="text-gray-700 dark:text-gray-300 font-light">
                                {{ $t('Seamlessly integrate our platform into your systems with user-friendly API documentation and expert support') }}
                            </p>
                        </div>
                    </div>

                    <div  @click="selectedFeature = 3" class="bg-white rounded-lg px-2 py-2 mb-5">
                        <div class="flex justify-between items-center cursor-pointer">
                            <div class="flex items-center space-x-3">
                                <div class="bg-secondary/10 rounded-lg flex items-center justify-center h-10 w-10">
                                    <svg class="h-6 w-6 text-secondary" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M9 13V5c0-1.1.9-2 2-2h9c1.1 0 2 .9 2 2v6h-3.43l-1.28-1.74a.14.14 0 0 0-.24 0L15.06 12c-.06.06-.18.07-.24 0l-1.43-1.75a.152.152 0 0 0-.23 0l-2.11 2.66c-.08.09-.01.24.11.24h6.34V15H11c-1.11 0-2-.89-2-2m-3 9v-1H4v1H2V2h2v1h2V2h2.39C7.54 2.74 7 3.8 7 5v8c0 2.21 1.79 4 4 4h4.7c-1.03.83-1.7 2.08-1.7 3.5c0 .53.11 1.03.28 1.5zM4 7h2V5H4zm0 4h2V9H4zm0 4h2v-2H4zm2 4v-2H4v2zm17-6v2h-2v5.5a2.5 2.5 0 0 1-5 0a2.5 2.5 0 0 1 3.5-2.29V13z"/></svg>
                                </div>
                                <h4 class="font-regular text-gray-800">{{ $t('Multi-media support') }}</h4>
                            </div>
                            <div>
                                <svg v-if="selectedFeature != 3" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M16.75 11.989a1.82 1.82 0 0 1-.57 1.36l-6.82 6.1a1.27 1.27 0 0 1-.65.31h-.19a1.3 1.3 0 0 1-.52-.1a1.23 1.23 0 0 1-.54-.47a1.19 1.19 0 0 1-.21-.68v-13a1.2 1.2 0 0 1 .21-.69a1.23 1.23 0 0 1 1.25-.56c.24.039.464.143.65.3l6.76 6.09c.19.162.344.363.45.59c.114.234.175.49.18.75"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M19.696 8.72a1.22 1.22 0 0 1-.3.64l-6.09 6.76a1.85 1.85 0 0 1-.58.46a1.7 1.7 0 0 1-1.42.03a1.75 1.75 0 0 1-.62-.42l-6.1-6.83a1.28 1.28 0 0 1-.31-.64a1.31 1.31 0 0 1 .56-1.26a1.36 1.36 0 0 1 .68-.21h13a1.293 1.293 0 0 1 1.15.76c.081.228.092.476.03.71"/></svg>
                            </div>
                        </div>
                        <div v-if="selectedFeature === 3" class="mt-2">
                            <p class="text-gray-700 dark:text-gray-300 font-light">
                                {{ $t('Enrich conversations effortlessly by sharing images, documents, and multimedia content, enhancing your overall communication strategy') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid lg:grid-cols-2 grid-cols-1 gap-6 items-center mt-10">
                <div class="bg-gray-300/10 rounded-[20px] px-5 md:px-10 py-10 order-2 lg:order-1" data-aos="fade-up" data-aos-duration="500">

                    <div class="bg-secondary/10  h-12 w-12 bg-primary/10 flex items-center justify-center rounded-lg">
                        <svg class="h-6 w-6 text-secondary" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 1024 1024"><path fill="currentColor" d="M288 384h448v64H288zm96-128h256v64H384zM131.456 512H384v128h256V512h252.544L721.856 192H302.144zM896 576H704v128H320V576H128v256h768zM275.776 128h472.448a32 32 0 0 1 28.608 17.664l179.84 359.552A32 32 0 0 1 960 519.552V864a32 32 0 0 1-32 32H96a32 32 0 0 1-32-32V519.552a32 32 0 0 1 3.392-14.336l179.776-359.552A32 32 0 0 1 275.776 128"/></svg>
                    </div>

                    <h1 class="text-xl/tight font-medium mt-6 mb-4">{{ $t('Seamless Bulk and Direct Messaging') }}</h1>
                    <p class="text-gray-500">{{ $t('Streamline your communication strategy with our platform\'s dual capabilities. Engage in effortless one-on-one conversations with your contacts while also harnessing the power to execute targeted bulk messaging campaigns') }}</p>
                </div>

                <div class="bg-gray-300/10 rounded-[20px] px-5 md:px-10 py-10 order-2 lg:order-1" data-aos="fade-up" data-aos-duration="500">

                    <div class="bg-secondary/10  h-12 w-12 bg-primary/10 flex items-center justify-center rounded-lg">
                        <svg class="h-6 w-6 text-secondary" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M18.5 10.255c0 .044 0 .089-.003.133A1.537 1.537 0 0 0 17.473 10c-.162 0-.32.025-.473.074V5.75a.75.75 0 0 0-.75-.75h-8.5a.75.75 0 0 0-.75.75v4.505c0 .414.336.75.75.75h8.276l-.01.025l-.003.012l-.45 1.384l-.01.026a1.625 1.625 0 0 1-.019.053H7.75a2.25 2.25 0 0 1-2.25-2.25V5.75A2.25 2.25 0 0 1 7.75 3.5h3.5v-.75a.75.75 0 0 1 .649-.743L12 2a.75.75 0 0 1 .743.649l.007.101l-.001.75h3.5a2.25 2.25 0 0 1 2.25 2.25zm-5.457 3.781l.112-.036H6.254a2.25 2.25 0 0 0-2.25 2.25v.907a3.75 3.75 0 0 0 1.305 2.844c1.563 1.343 3.802 2 6.691 2c2.076 0 3.817-.339 5.213-1.028a1.545 1.545 0 0 1-1.169-1.003l-.004-.012l-.03-.093c-1.086.422-2.42.636-4.01.636c-2.559 0-4.455-.556-5.713-1.638a2.25 2.25 0 0 1-.783-1.706v-.907a.75.75 0 0 1 .75-.75H12v-.003a1.543 1.543 0 0 1 1.031-1.456zM10.999 7.75a1.25 1.25 0 1 0-2.499 0a1.25 1.25 0 0 0 2.499 0m3.243-1.25a1.25 1.25 0 1 1 0 2.499a1.25 1.25 0 0 1 0-2.499m1.847 10.912a2.831 2.831 0 0 0-1.348-.955l-1.377-.448a.544.544 0 0 1 0-1.025l1.377-.448a2.84 2.84 0 0 0 1.76-1.762l.01-.034l.449-1.377a.544.544 0 0 1 1.026 0l.448 1.377a2.837 2.837 0 0 0 1.798 1.796l1.378.448l.027.007a.544.544 0 0 1 0 1.025l-1.378.448a2.839 2.839 0 0 0-1.798 1.796l-.447 1.377a.55.55 0 0 1-.2.263a.544.544 0 0 1-.827-.263l-.448-1.377a2.834 2.834 0 0 0-.45-.848m7.694 3.801l-.765-.248a1.577 1.577 0 0 1-.999-.998l-.249-.765a.302.302 0 0 0-.57 0l-.249.764a1.577 1.577 0 0 1-.983.999l-.766.248a.302.302 0 0 0 0 .57l.766.249a1.576 1.576 0 0 1 .999 1.002l.248.764a.303.303 0 0 0 .57 0l.25-.764a1.575 1.575 0 0 1 .998-.999l.766-.248a.302.302 0 0 0 0-.57z"/></svg>
                    </div>

                    <h1 class="text-xl/tight font-medium mt-6 mb-4">{{ $t('Craft automated responses') }}</h1>
                    <p class="text-gray-500">{{ $t('Take control of your operational efficiency and streamline your workflow effortlessly with our customizable automated response system. Craft responses tailored to your unique needs, guaranteeing swift message delivery to your audience') }}</p>
                </div>
            </div>
        </section>

        <section id="section3" class="px-5 md:px-10 lg:px-20 2xl:px-60">
            <div>
                <div class="flex justify-center mb-2">
                    <div class="bg-secondary/10 text-secondary py-1 px-4 inline-block rounded-full mb-6 aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
                        <a href="#">
                            <div class="flex items-center gap-2">
                                <div>{{ $t('How it works') }}</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="flex justify-center">
                    <h2 class="text-center text-3xl md:text-3xl font-bold">{{ $t('A step-by-step guide to our platform') }}</h2>
                </div>
                <div class="flex justify-center">
                    <h3 class="text-center text-[18px] mt-5 w-[33em]">{{ $t('Explore our platform with ease! Sign up, connect your whatsapp account, and start communicating with your customers') }}</h3>
                </div>
                <div class="flex justify-between items-center gap-6 md:gap-4 lg:gap-6 stp-15 relative max-md:flex-col mt-10">
                    <img :src="'/images/shapes/stepArrow1.png'" alt="" class="absolute top-14 right-[22%] max-xxl:right-[23%] max-xxl:top-24 max-xxl:w-[200px] max-lg:right-[25%] max-lg:w-[100px] max-md:hidden">
                    <img :src="'/images/shapes/stepArrow2.png'" alt="" class="absolute top-8 left-[21%] max-xxl:top-20 max-xxl:left-[23%] max-xxl:w-[200px] max-lg:left-[25%] max-lg:w-[100px] max-md:hidden">
                    <div class="flex justify-center items-center text-center flex-col max-w-[350px]">
                        <div class="text-s1 text-secondary bg-softBg border border-strokeColor rounded-full p-6 lg:p-7 text-3xl lg:text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><defs><mask id="letsIconsUserScanDuotoneLine0"><g fill="none" stroke-linecap="round"><path stroke="silver" stroke-opacity="0.25" d="M17.5 3.5h.2c1.791 0 2.687 0 3.244.556c.556.557.556 1.453.556 3.244v.2m-4 13h.2c1.791 0 2.687 0 3.244-.556c.556-.557.556-1.453.556-3.244v-.2m-15-13h-.2c-1.791 0-2.687 0-3.243.556C2.5 4.614 2.5 5.51 2.5 7.3v.2m4 13h-.2c-1.791 0-2.687 0-3.243-.556C2.5 19.387 2.5 18.49 2.5 16.7v-.2"/><path stroke="#fff" d="M7.215 15.785c.473-.683 1.16-1.26 2.002-1.665A6.448 6.448 0 0 1 12 13.5c.981 0 1.941.216 2.783.62c.842.405 1.53.982 2.002 1.665"/><circle cx="12" cy="9" r="2.5" stroke="#fff"/></g></mask></defs><path fill="currentColor" d="M0 0h24v24H0z" mask="url(#letsIconsUserScanDuotoneLine0)"/></svg>
                        </div>
                        <h4 class="text-xl font-medium pt-8 pb-6">{{ $t('Create an account') }}</h4>
                        <p class="font-light">{{ $t('Get started in a few clicks. Seamless registration, hassle-free access to a world of possibilities awaits you') }}</p>
                    </div>
                    <div class="flex justify-center items-center text-center flex-col max-w-[350px]">
                        <div class="text-s1 text-secondary bg-softBg border border-strokeColor rounded-full p-6 lg:p-7 text-3xl lg:text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 48 48"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M40.47 14.14v-3.21H10.75a3.23 3.23 0 0 0-3.22 3.21h0v18H4v4.94h23.51v-4.95H10.75v-18Z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M32.14 17.36A1.61 1.61 0 0 0 30.53 19v16.5a1.61 1.61 0 0 0 1.61 1.6h10.25A1.61 1.61 0 0 0 44 35.49V19a1.61 1.61 0 0 0-1.61-1.61h0Zm8.64 14.77h-7V20.58h7Z"/></svg>
                        </div>
                        <h4 class="text-xl font-medium pt-8 pb-6">{{ $t('Setup your whatsapp instance') }}</h4>
                        <p class="font-light">{{ $t('Quick and easy setup for your personalized WhatsApp instance. Begin engaging with your contacts in no time') }}.</p>
                    </div>
                    <div class="flex justify-center items-center text-center flex-col max-w-[350px]">
                        <div class="text-s1 text-secondary bg-softBg border border-strokeColor rounded-full p-6 lg:p-7 text-3xl lg:text-5xl">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 48 48"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M16.8 29.286c-6.247 0-11.3-5.33-11.3-11.893S10.553 5.5 16.8 5.5h11.757c6.247 0 11.3 5.33 11.3 11.893s-5.053 11.893-11.3 11.893z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M24.42 34.092v7.252c0 .63-.508 1.156-1.117 1.156a1.096 1.096 0 0 1-.788-.342l-11.02-11.35c-2.159-2.208-3.352-5.23-3.352-8.356c0-6.438 5.053-11.667 11.3-11.667H31.2c6.247 0 11.3 5.23 11.3 11.667s-5.053 11.666-11.3 11.666h-6.78z"/></svg>
                        </div>
                        <h4 class="text-xl font-medium pt-8 pb-6">{{ $t('Start your first campaign') }}</h4>
                        <p class="font-light">{{ $t('Embark on your journey to success. Initiate your inaugural campaign effortlessly and witness the power of impactful outreach') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="section4" class="relative px-5 md:px-10 lg:px-20 py-20 2xl:px-60">
            <div>
                <div class="relative">

                    <div class="text-center">
                        <div class="flex justify-center mb-2">
                            <div class="bg-secondary/10 text-secondary py-1 px-4 inline-block rounded-full mb-4 aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
                                <a href="#">
                                    <div class="flex items-center gap-2">
                                        <div>{{ $t('Plans') }}</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <h1 class="text-3xl md:text-5xl font-bold mb-4">{{ $t('Flexible pricing') }}</h1>
                        <div class="flex justify-center">
                            <h3 class="text-center text-[18px] mt-5 w-[33em]">{{ $t('Whether you\'re an individual, a small team, or a growing enterprise, we have a plan that aligns perfectly with your goals') }}</h3>
                        </div>
                    </div>

                    <div class="grid xl:grid-cols-3 md:grid-cols-2 lg:grid-cols-3 grid-cols-1 gap-5 mt-14">

                        <!-- 2 -->
                        <div v-for="(item, index) in props.plans" :key="index" data-aos="fade-up" data-aos-duration="700" class="aos-init aos-animate">
                            <div class="transition-all duration-300 pointer-events-auto">
                                <div class="bg-gray-300/10 w-full h-full p-10 rounded-[12px]">
                                    <h2 class="text-xl text-[#034737]">{{ item.name }}</h2>
                                    <h1 class="text-3xl/tight font-semibold mt-3">
                                        <sup class="text-gray-500 text-sm font-normal">{{ props.currency ?? '$' }}</sup> 
                                        {{ item.price }} 
                                        <sub v-if="item.period === 'monthly'" class="text-gray-500 text-sm font-normal">{{ $t('Per month') }}</sub>
                                        <sub v-if="item.period === 'yearly'" class="text-gray-500 text-sm font-normal">{{ $t('Per year') }}</sub>
                                    </h1>
                                    <div class="flex mt-8">
                                        <Link href="/signup" class="bg-primary hover:bg-secondary text-center text-white w-full py-3 rounded-lg border border-transparent focus:outline focus:outline-primary/50 transition-all duration-300">{{ $t('Select Plan') }}</Link>
                                    </div>
                                    <div class="mt-5">
                                        <div class="flex flex-col space-y-2">
                                            <div class="flex items-center text-gray-600 gap-3">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 16 16"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25"/><path d="m5.75 7.75l2.5 2.5l6-6.5"/></g></svg>
                                                </span>
                                                <h3>{{ getDetail(item?.metadata, 'campaign_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'campaign_limit') }} {{ $t('Campaigns') }}</h3>
                                            </div>
                                            <div class="flex items-center text-gray-600 gap-3">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 16 16"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25"/><path d="m5.75 7.75l2.5 2.5l6-6.5"/></g></svg>
                                                </span>
                                                <h3>{{ getDetail(item?.metadata, 'message_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'message_limit') }} {{ $t('Messages') }}</h3>
                                            </div>
                                            <div class="flex items-center text-gray-600 gap-3">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 16 16"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25"/><path d="m5.75 7.75l2.5 2.5l6-6.5"/></g></svg>
                                                </span>
                                                <h3>{{ getDetail(item?.metadata, 'contacts_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'contacts_limit') }} {{ $t('Contacts') }}</h3>
                                            </div>
                                            <div class="flex items-center text-gray-600 gap-3">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 16 16"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25"/><path d="m5.75 7.75l2.5 2.5l6-6.5"/></g></svg>
                                                </span>
                                                <h3>{{ getDetail(item?.metadata, 'canned_replies_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'canned_replies_limit') }} {{ $t('Canned replies') }}</h3>
                                            </div>
                                            <div class="flex items-center text-gray-600 gap-3">
                                                <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 16 16"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M14.25 8.75c-.5 2.5-2.385 4.854-5.03 5.38A6.25 6.25 0 0 1 3.373 3.798C5.187 1.8 8.25 1.25 10.75 2.25"/><path d="m5.75 7.75l2.5 2.5l6-6.5"/></g></svg>
                                                </span>
                                                <h3>{{ getDetail(item?.metadata, 'team_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'team_limit') }} {{ $t('Users') }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="section5" class="bg-gray-300/10 px-5 md:px-10 lg:px-20 2xl:px-60 py-10">
            <div>
                <div class="flex justify-center mb-2">
                    <div class="bg-secondary/10 text-secondary py-1 px-4 inline-block rounded-full mb-6 aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
                        <a href="#">
                            <div class="flex items-center gap-2">
                                <div>{{ $t('Reviews') }}</div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="flex justify-center">
                    <h2 class="text-center text-3xl md:text-3xl font-bold">{{ $t('Explore genuine client feedback') }}</h2>
                </div>
                <div class="flex justify-center">
                    <h3 class="text-center text-[18px] mt-5 w-[30em]">{{ $t('Discover how businesses like yours transformed with our software. Real stories of growth, innovation, and success') }}</h3>
                </div>
                <div class="md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-6 space-y-4 md:space-y-0 mt-20">
                    <div v-for="(item, index) in props.reviews" :key="index" class="bg-white rounded-[15px] p-5">
                        <div class="flex">
                            <svg :class="item.rating >= 1 ? 'text-[#f7ba2f]' : 'text-gray-300'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12.86 10.44L11 6.06l-1.86 4.39l-4.75.41L8 14l-1.08 4.63L11 16.17l4.09 2.46L14 14l3.61-3.14zm3.73 10.26L11 17.34L5.42 20.7l1.46-6.35l-4.92-4.28l6.49-.57l2.55-6l2.55 6l6.49.57l-4.92 4.27z"/></svg>
                            <svg :class="item.rating >= 2 ? 'text-[#f7ba2f]' : 'text-gray-300'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12.86 10.44L11 6.06l-1.86 4.39l-4.75.41L8 14l-1.08 4.63L11 16.17l4.09 2.46L14 14l3.61-3.14zm3.73 10.26L11 17.34L5.42 20.7l1.46-6.35l-4.92-4.28l6.49-.57l2.55-6l2.55 6l6.49.57l-4.92 4.27z"/></svg>
                            <svg :class="item.rating >= 3 ? 'text-[#f7ba2f]' : 'text-gray-300'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12.86 10.44L11 6.06l-1.86 4.39l-4.75.41L8 14l-1.08 4.63L11 16.17l4.09 2.46L14 14l3.61-3.14zm3.73 10.26L11 17.34L5.42 20.7l1.46-6.35l-4.92-4.28l6.49-.57l2.55-6l2.55 6l6.49.57l-4.92 4.27z"/></svg>
                            <svg :class="item.rating >= 4 ? 'text-[#f7ba2f]' : 'text-gray-300'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12.86 10.44L11 6.06l-1.86 4.39l-4.75.41L8 14l-1.08 4.63L11 16.17l4.09 2.46L14 14l3.61-3.14zm3.73 10.26L11 17.34L5.42 20.7l1.46-6.35l-4.92-4.28l6.49-.57l2.55-6l2.55 6l6.49.57l-4.92 4.27z"/></svg>
                            <svg :class="item.rating == 5 ? 'text-[#f7ba2f]' : 'text-gray-300'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12.86 10.44L11 6.06l-1.86 4.39l-4.75.41L8 14l-1.08 4.63L11 16.17l4.09 2.46L14 14l3.61-3.14zm3.73 10.26L11 17.34L5.42 20.7l1.46-6.35l-4.92-4.28l6.49-.57l2.55-6l2.55 6l6.49.57l-4.92 4.27z"/></svg>
                        </div>
                        <p class="font-light">{{ item.review }}</p>
                        <div class="flex justify-between items-center mt-5">
                            <div>
                                <h3 class="font-semibold">{{ item.name }}</h3>
                                <p class="font-light">{{ item.position }}</p>
                            </div>
                            <div>
                                <svg class="text-gray-500/10" xmlns="http://www.w3.org/2000/svg" width="3em" height="3em" viewBox="0 0 24 24"><path fill="currentColor" d="M19.417 6.679C20.447 7.773 21 9 21 10.989c0 3.5-2.456 6.637-6.03 8.188l-.893-1.378c3.335-1.804 3.987-4.145 4.248-5.621c-.537.278-1.24.375-1.93.311c-1.804-.167-3.226-1.648-3.226-3.489a3.5 3.5 0 0 1 3.5-3.5c1.073 0 2.1.49 2.748 1.179m-10 0C10.447 7.773 11 9 11 10.989c0 3.5-2.456 6.637-6.03 8.188l-.893-1.378c3.335-1.804 3.987-4.145 4.247-5.621c-.537.278-1.24.375-1.929.311C4.591 12.323 3.17 10.842 3.17 9a3.5 3.5 0 0 1 3.5-3.5c1.073 0 2.1.49 2.748 1.179"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section id="section6" class="px-5 md:px-10 lg:px-20 py-10">
            <div class="aos-init aos-animate" data-aos="fade-up" data-aos-duration="2000">

                <div class="text-center">
                    <div class="flex justify-center mb-2">
                        <div class="bg-secondary/10 text-secondary py-1 px-4 inline-block rounded-full mb-4 aos-init aos-animate" data-aos="fade-right" data-aos-duration="1000">
                            <a href="#">
                                <div class="flex items-center gap-2">
                                    <div>{{ $t('FAQs') }}</div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <h2 class="text-3xl md:text-5xl/tight font-bold mt-3 mb-4">{{ $t('Frequently asked questions') }}</h2>
                    <div class="flex justify-center">
                        <h3 class="text-center text-[18px] mt-5 w-[32em]">{{ $t('Learn how our platform works and find solutions to common queries. Need more help? Reach out to our support team for personalized assistance') }}</h3>
                    </div>
                </div>

                <div data-fc-type="accordion" class="mt-14 lg:w-3/4 lg:mx-auto 2xl:w-2/3 space-y-2 md:px-20">
                    <!-- 1 -->
                    <div v-for="(item, index) in props.faqs.data" :key="index" class="border border-gray-300 rounded-lg">
                        <button @click="selectedFaq = index+1" class="inline-flex p-5 items-center justify-between w-full text-left transition fc-collapse" data-fc-type="collapse">
                            <div v-html="item.question"></div>
                            <div>
                                <svg v-if="selectedFaq != index+1" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M16.75 11.989a1.82 1.82 0 0 1-.57 1.36l-6.82 6.1a1.27 1.27 0 0 1-.65.31h-.19a1.3 1.3 0 0 1-.52-.1a1.23 1.23 0 0 1-.54-.47a1.19 1.19 0 0 1-.21-.68v-13a1.2 1.2 0 0 1 .21-.69a1.23 1.23 0 0 1 1.25-.56c.24.039.464.143.65.3l6.76 6.09c.19.162.344.363.45.59c.114.234.175.49.18.75"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path fill="currentColor" d="M19.696 8.72a1.22 1.22 0 0 1-.3.64l-6.09 6.76a1.85 1.85 0 0 1-.58.46a1.7 1.7 0 0 1-1.42.03a1.75 1.75 0 0 1-.62-.42l-6.1-6.83a1.28 1.28 0 0 1-.31-.64a1.31 1.31 0 0 1 .56-1.26a1.36 1.36 0 0 1 .68-.21h13a1.293 1.293 0 0 1 1.15.76c.081.228.092.476.03.71"/></svg>
                            </div>
                        </button>
                        <div class="w-full overflow-hidden transition-[height] duration-300" :class="selectedFaq === index+1 ? '' : 'hidden'" style="">
                            <div class="text-slate-500 dark:text-gray-300 pt-3 p-5" v-html="item.answer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer id="section7" class="bg-gray-300/10 px-5 md:px-10 lg:px-20 md:pt-10 pb-5 2xl:px-60">
            <div class="md:flex">
                <div class="md:w-1/4 lg:w-1/2">
                    <div>
                        <img class="max-w-[150px]" v-if="props.companyConfig.logo" :src="'/media/' + props.companyConfig.logo" :alt="props.companyConfig.company_name">
                        <h4 v-else class="text-2xl mb-2">{{ props.companyConfig.company_name }}</h4>
                        <div style="margin-right: 21%;margin-top: 20px;">
                        {{ $t('With WhatsApp.io, we ensure that you never miss a message. Our team is available to chat, resolve your queries, and assist you in real time.')}}
                        </div>
                    </div>
                </div>
                <div class="md:w-3/4 lg:w-1/2 md:flex md:justify-between text-sm space-y-8 md:space-y-0">
                    <div>
                        <h4 class="text-xl font-regular mb-2">{{ $t('Links') }}</h4>
                        <div class="flex md:justify-between space-x-10 md:space-x-10 lg:space-x-20 space-y-2 md:space-y-0">
                            <div class="space-y-2">
                                <div>
                                    <a href="#section2" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('Features') }}</a>
                                </div>
                                <div>
                                    <a href="#section3" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('How it works') }}</a>
                                </div>
                                <div>
                                    <a href="#section4" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('Pricing') }}</a>
                                </div>
                                <div>
                                    <a href="#section5" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('Reviews') }}</a>
                                </div>
                                <div>
                                    <a href="#section6" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t('FAQs') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <h4 class="text-xl font-regular mb-2">{{ $t('Information') }}</h4>
                            <div v-if="pages.length > 0" class="space-y-2">
                                <div v-for="page in pages" :key="page.id">
                                    <Link :href="'/pages/' + formattedName(page.name)" class="border-b-2 border-[#fafbfb] hover:border-b-2 hover:border-black">{{ $t(page.name) }}</Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-10 border-t pt-5 text-sm">
                <p class="font-light">{{ $t('Copyright') }} {{ currentYear }}. {{ $t('All rights reserved') }}.</p>
            </div>
        </footer>

        <!-- Floating WhatsApp Icon -->
        <a href="https://wa.me/919916225399?text=Hi%2C%20I%20am%20interested" target="_blank" class="fixed bottom-5 right-5 bg-green-500 text-white p-3 rounded-full shadow-lg hover:bg-green-600 transition duration-300">
            <svg class="w-8 h-8" viewBox="0 0 32 32" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 2.5C8.544 2.5 2.5 8.544 2.5 16c0 2.75.732 5.453 2.108 7.844L2 30l6.309-2.553A13.373 13.373 0 0 0 16 29.5C23.456 29.5 29.5 23.456 29.5 16S23.456 2.5 16 2.5Zm0 2c6.69 0 12.5 5.81 12.5 12.5S22.69 27.5 16 27.5a11.44 11.44 0 0 1-5.963-1.65l-.429-.256-3.875 1.568 1.49-3.727-.276-.44A10.46 10.46 0 0 1 5.5 16C5.5 9.31 11.31 4.5 16 4.5Zm-4.025 7.22c-.276 0-.588.008-.912.035a1.36 1.36 0 0 0-.964.453c-.304.34-.988.964-.988 2.34 0 1.376.656 2.713.752 2.9.096.188 1.296 2.6 3.276 3.744 1.98 1.144 2.376 1.036 2.808.968.432-.068 1.38-.56 1.58-1.104.2-.544.2-1.012.144-1.104-.056-.092-.216-.144-.448-.252s-1.38-.68-1.596-.76c-.216-.08-.372-.12-.528.12-.156.24-.608.76-.748.92s-.276.18-.516.06c-.24-.12-.96-.36-1.836-1.144-.68-.608-1.144-1.364-1.28-1.6s-.014-.36.092-.48c.104-.112.228-.24.34-.36s.22-.24.336-.384c.112-.144.072-.264.048-.36-.024-.096-.528-1.32-.72-1.808-.192-.488-.384-.4-.528-.4Z"/>
            </svg>
        </a>

    </div>
</template>
<script setup>
    import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
    import { Link } from "@inertiajs/vue3";
    import LangToggle from '@/Components/LangToggle.vue';

    const props = defineProps({ faqs: Object, plans: Object, reviews: Object, companyConfig: Object, languages: Object, currentLanguage:String, currency: String, pages: Object });

    const currentYear = new Date().getFullYear();
    const facebookUrl = ref(null);
    const twitterUrl = ref(null);
    const instagramUrl = ref(null);
    const slackUrl = ref(null);
    const linkedinUrl = ref(null);

    const parseSocials = () => {
        try {
            const socialsArray = JSON.parse(props.companyConfig.socials);

            facebookUrl.value = socialsArray['facebook'] || null;
            twitterUrl.value = socialsArray['twitter'] || null;
            instagramUrl.value = socialsArray['instagram'] || null;
            slackUrl.value = socialsArray['slack'] || null;
            linkedinUrl.value = socialsArray['linkedin'] || null;
        } catch (error) {
            console.error('Error parsing socials:', error);
        }
    };

    const getDetail = (value, key) => {
        if(value){
            const item = JSON.parse(value);
            return item?.[key] ?? null;
        } else {
            return null;
        }
    }

    const formattedName = computed(() => {
        return (value) => {
            return value.trim().toLowerCase().replace(/\s+/g, '-');
        };
    });

    const selectedFeature = ref(1);
    const selectedFaq = ref(1);

    onMounted(() => {
        parseSocials();
    });
</script>