<body class="hold-transition sidebar-mini layout-fixed">

    {{-- ========================================================= --}}
    {{-- Global Application Loader --}}
    {{-- ========================================================= --}}

    @persist('app-loader')

        <div
            id="app-loader"
            class="app-loader is-active"
            aria-hidden="true"
        >

            {{-- Animated Background --}}
            <div class="app-loader-bg">

                <svg
                    class="app-loader-shape app-loader-shape-1"
                    viewBox="0 0 200 200"
                    aria-hidden="true"
                >
                    <circle
                        cx="100"
                        cy="100"
                        r="80"
                    />
                </svg>


                <svg
                    class="app-loader-shape app-loader-shape-2"
                    viewBox="0 0 200 200"
                    aria-hidden="true"
                >
                    <circle
                        cx="100"
                        cy="100"
                        r="70"
                    />
                </svg>


                <svg
                    class="app-loader-grid"
                    viewBox="0 0 400 400"
                    aria-hidden="true"
                >
                    <defs>

                        <pattern
                            id="loader-grid-pattern"
                            width="30"
                            height="30"
                            patternUnits="userSpaceOnUse"
                        >
                            <path d="M 30 0 L 0 0 0 30" />
                        </pattern>

                    </defs>

                    <rect
                        width="400"
                        height="400"
                        fill="url(#loader-grid-pattern)"
                    />

                </svg>

            </div>


            {{-- Loader Content --}}
            <div class="app-loader-content">

                <div class="app-loader-icon">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >

                        <rect
                            x="3"
                            y="3"
                            width="18"
                            height="18"
                            rx="5"
                        />

                        <path
                            d="M8 12.5L10.5 15L16.5 9"
                        />

                    </svg>

                </div>


                <div class="app-loader-title">
                    Loading
                </div>


                <div class="app-loader-subtitle">
                    Preparing your workspace...
                </div>


                <div class="app-loader-dots">

                    <span></span>
                    <span></span>
                    <span></span>

                </div>

            </div>

        </div>

    @endpersist


    {{-- ========================================================= --}}
    {{-- Application --}}
    {{-- ========================================================= --}}

    <div class="wrapper">

        {{-- Sidebar / Navbar / Content --}}