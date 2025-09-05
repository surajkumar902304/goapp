@extends('layouts.app')

@section('content')
    <div class="hero py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-7 col-xl-8">
                    <h1 class="display-5" data-aos="fade-up-right">Easiest app development platform</h1>
                    <h2 class="display-4 fw-bold" data-aos="fade-up-right">The best no-code<br>
                        mobile app builder</h2>
                    <p class="fs-5" data-aos="fade-up-right">
                        TrueWebApp is designed to make app development simple for everyone. Take a tour & explore the full scope of our no-code app development solution. Get, set, scroll & explore TrueWebApp inside out!
                    </p>
                    <a href="#" class="btn btn-outline-primary fw-bold rounded-0 btn-lg">Get Started</a>
                </div>
                <div class="col-md-6 col-lg-5 col-xl-4" data-aos="fade-up-right">
                    <img src="{{asset('images/mobileapp1.png')}}" alt="Truewebapp" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold display-5" data-aos="zoom-in">Simplified app building with powerful tools</h2>
            <h3 class="text-center h6">Effortlessly create, customize, and launch professional native apps with tools that simplify every step.</h3>
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-6">
                    <div class="card mb-4 shadow" data-aos="fade-right">
                        <div class="card-body">
                            <div class="card-title h3 fw-bold">Effortless app creation</div>
                            Build robust, professional apps with ease using TrueWebApp's no-code platform. Customize every detail without any technical skills.
                        </div>
                    </div>
                    <div class="card mb-4 shadow" data-aos="fade-right">
                        <div class="card-body">
                            <div class="card-title h3 fw-bold">Personalize</div>
                            Customize app layout and monitor every design & content update on the live preview.
                        </div>
                    </div>
                    <div class="card mb-4 shadow" data-aos="fade-right">
                        <div class="card-body">
                            <div class="card-title h3 fw-bold">Preview in real-time</div>
                            See your app take shape instantly with live previews. Review changes as you make them, ensuring a perfect final product.
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6" data-aos="fade-up">
                    <img src="{{asset('images/mobileapp2.webp')}}" alt="Truewebapp" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
    <section id="features" class="features py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold display-5" data-aos="zoom-in">Take absolute control of your app</h2>
            <h3 class="h6 text-center">Configure multiple app settings based on your goals and the needs of your end users.
                From user-login to checkout, configure everything with a few clicks.</h3>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 mt-5 g-5">
                <div class="col" data-aos="zoom-in">
                    <div class="card shadow border-success border-4 border-start border-end-0 border-top-0">
                        <div class="card-body">
                            <span class="iconify display-3" data-icon="carbon:update-now"></span>
                            <div class="card-title fw-bold h3 fw-bold">Update app information</div>
                            <div>
                                Add app details, configure a consent pop-up, enter a custom share link, and more seamlessly.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="zoom-in">
                    <div class="card shadow border-primary border-4 border-start border-end-0 border-top-0">
                        <div class="card-body">
                            <span class="iconify display-3" data-icon="si:dashboard-customize-duotone"></span>
                            <div class="card-title fw-bold h3 fw-bold">Customize your app menu</div>
                            <div>
                                Customize your app’s side & bottom menu effortlessly. Build a native menu or sync one from your website.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="zoom-in">
                    <div class="card shadow border-danger border-4 border-start border-end-0 border-top-0">
                        <div class="card-body">
                            <span class="iconify display-3" data-icon="ph:users-four-light"></span>
                            <div class="card-title fw-bold h3 fw-bold">Manage user onboarding</div>
                            <div>
                                Control your app availability and also manage how your users login and sign-up to your app.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="zoom-in">
                    <div class="card shadow border-info border-4 border-start border-end-0 border-top-0">
                        <div class="card-body">
                            <span class="iconify display-3" data-icon="gg:home-screen"></span>
                            <div class="card-title fw-bold h3 fw-bold">Configure product screens</div>
                            <div>
                                Manage how products are displayed in your app. Enable webviews, discount badges, and more.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="zoom-in">
                    <div class="card shadow border-dark border-4 border-start border-end-0 border-top-0">
                        <div class="card-body">
                            <span class="iconify display-3" data-icon="material-symbols-light:shopping-cart-checkout-sharp"></span>
                            <div class="card-title fw-bold h3 fw-bold">Optimize app checkout</div>
                            <div>
                                Control the options customers have during checkout and offer a frictionless app experience.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col" data-aos="zoom-in">
                    <div class="card shadow border-warning border-4 border-start border-end-0 border-top-0">
                        <div class="card-body">
                            <span class="iconify display-3" data-icon="gridicons:posts"></span>
                            <div class="card-title fw-bold h3 fw-bold">Manage posts & pages </div>
                            <div>
                                Manage how your website’s posts and pages appear in the mobile app and optimize user experience.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="industries" class="products py-5">
        <div class="container">
            <h2 class="text-center fw-bold display-5" data-aos="zoom-in">An app maker for every business</h2>
            <h3 class="h6 text-center">Create purpose-built, reliable apps tailored for any industry with simple drag and drop features. The powerful app builder enables easy design and launch of high-performance apps without the need for coding.</h3>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 mt-5 g-5">
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/restaurant-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Restaurant App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/grocery-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Grocery App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/bakery-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Bakery App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/clothing-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Clothing App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/education-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Education App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/real-estate-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Realestate App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/ecommerce-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Ecommerce App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/health-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Health App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/taxi-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Taxi App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/travel-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Travel App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/pharmacy-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Pharmacy App</div>
                        </div>
                    </div>
                </div>
                <div class="col text-center" data-aos="flip-left">
                    <div class="card shadow border-success border-4 border-start-0 border-bottom-0 border-end-0">
                        <img class="card-img-top" src="{{asset('images/jewellery-app.webp')}}" alt="products">
                        <div class="card-body">
                            <div class="card-title fw-bold h3 fw-bold">Jewellery App</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="pricing" class="prices py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold display-5" data-aos="zoom-in">Donation for community</h2>
            <h3 class="h5 text-center text-uppercase fw-bold">Our Pricing</h3>
            <div class="row mt-3 row-cols-1 row-cols-sm-2 row-cols-md-3 g-5">
                <div class="col">
                    <div class="card">
                        <div class="card-header text-bg-info">
                            <h2 class="text-center">Starter</h2>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Android app
                                    <i class="iconify fs-3" data-icon="carbon:application-mobile"></i>
                                    <i class="iconify text-success fs-3" data-icon="devicon:android"></i>
                                </li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Convert any website</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    In-app browser</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Customizable design</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Real device testing</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Push notifications</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Monetization features</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Team collaboration</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Trueweb Branding</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-info rounded-0 fw-bold">Start Now</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header text-bg-info">
                            <h2 class="text-center">Pro</h2>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Android & IOS app
                                    <i class="iconify fs-3" data-icon="carbon:application-mobile"></i>
                                    <i class="iconify text-success fs-3" data-icon="devicon:android"></i>
                                    <i class="iconify fs-3" data-icon="fa:apple"></i>
                                </li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Convert any website</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    In-app browser</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Customizable design</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Real device testing</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Push notifications</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Monetization features</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Team collaboration</li>
                                <li class="list-group-item">
                                    <i class="iconify text-danger fs-3" data-icon="charm:circle-cross"></i>
                                    No Trueweb Branding</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-info rounded-0 fw-bold">Start Now</button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card">
                        <div class="card-header text-bg-info">
                            <h2 class="text-center">Premium</h2>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Android & IOS app
                                    <i class="iconify fs-3" data-icon="carbon:application-mobile"></i>
                                    <i class="iconify fs-3" data-icon="devicon:android"></i>
                                    <i class="iconify fs-3" data-icon="fa:apple"></i>
                                </li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Convert any website</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    In-app browser</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Customizable design</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Real device testing</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Push notifications</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Monetization features</li>
                                <li class="list-group-item">
                                    <i class="iconify text-success fs-3" data-icon="uil:check-circle"></i>
                                    Team collaboration</li>
                                <li class="list-group-item">
                                    <i class="iconify text-danger fs-3" data-icon="charm:circle-cross"></i>
                                    No Trueweb Branding</li>
                            </ul>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-info rounded-0 fw-bold">Start Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="about" class="about py-5">
        <div class="container">
            <h2 class="text-center fw-bold display-5" data-aos="zoom-in">About TrueWebApp</h2>
            <h3 class="text-center fw-bold">Loved by 100+ happy customers</h3>
            <div class="row row-cols-1 row-cols-md-2 mt-4 align-items-center">
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title h3 fw-bold">Apps for iOS</div>
                            <div>Creating iOS apps and publishing them to the App Store can be tricky, but not with TrueWebApp. Here, you can create and publish iOS apps that are carefully constructed for approval from Apple App Store.</div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title h3 fw-bold">Apps for Android</div>
                            <div>When creating mobile apps for Android users, you have a huge potential user base to reach. Build and publish the perfect mobile app with TrueWebApp and go live on Google Play Store effortlessly.</div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <img src="{{asset('images/contactus1.webp')}}" alt="" class="img-fluid">
                </div>

            </div>
        </div>
    </section>
    <section id="contact" class="prices py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold display-5">Contact Us</h2>
            <h3 class="h6 text-center">Make Mobile app with your own innovation and knowledge.</h3>
            <div class="row align-items-center mt-5">
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="row gy-3">
                                <div class="col-12">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" placeholder="John Doe" name="name" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" placeholder="email@gmail.com" name="email" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="number" placeholder="7766554433" name="phone" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea name="message" class="form-control" rows="4" required></textarea>
                                </div>
                                <div class="col-12 text-center">
                                   <button class="btn btn-success rounded-0">Submit Query</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5">
                    <img src="{{asset('images/contactus1.webp')}}" alt="contact" class="img-fluid">
                </div>
            </div>
        </div>
    </section>
@endsection
