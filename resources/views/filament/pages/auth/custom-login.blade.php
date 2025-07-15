{{-- resources/views/filament/pages/auth/custom-login.blade.php --}}
<div>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="flex w-full max-w-6xl items-center justify-center space-x-8">
            {{-- Phone Mockup (Optional for larger screens) --}}
            <div class="hidden lg:flex items-center justify-center flex-1">
                <div class="relative">
                    <div class="w-64 h-[500px] border-4 border-gray-800 rounded-[2rem] bg-white shadow-xl">
                        <div class="w-full h-full bg-gradient-to-br from-teal-400 to-teal-600 rounded-[1.5rem] relative overflow-hidden">
                            <div class="absolute top-4 left-4 w-2 h-2 bg-black rounded-full opacity-30"></div>
                            <div class="absolute top-4 left-8 w-8 h-1 bg-black rounded-full opacity-30"></div>
                        </div>
                    </div>
                    <div class="absolute -right-8 top-1/2 transform -translate-y-1/2 w-56 h-[480px] border-4 border-gray-800 rounded-[2rem] bg-teal-600 shadow-xl">
                    </div>
                </div>
            </div>

            {{-- Login Form --}}
            <div class="w-full max-w-md flex-1">
                <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200">
                    {{-- Logo and Title --}}
                    <div class="text-center mb-8">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-teal-100">
                            <svg class="h-8 w-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Log in</h2>
                    </div>

                    {{-- Form --}}
                    <div class="space-y-6">
                        {{ $this->form }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom styling untuk form */
        .fi-fo-field-wrp {
            margin-bottom: 1.5rem;
        }
        
        .fi-fo-field-wrp label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .fi-input {
            width: 100%;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease-in-out;
            background-color: #fff;
        }
        
        .fi-input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
            outline: none;
        }
        
        .fi-input::placeholder {
            color: #9ca3af;
        }
        
        /* Button styling */
        .fi-ac-action {
            width: 100%;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .fi-btn {
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            width: 100%;
            justify-content: center;
        }
        
        .fi-btn-color-primary {
            background-color: #0d9488;
            border-color: #0d9488;
            color: white;
        }
        
        .fi-btn-color-primary:hover {
            background-color: #0f766e;
            border-color: #0f766e;
        }
        
        /* Google button styling */
        .fi-btn-color-gray {
            background-color: white;
            border: 2px solid #d1d5db;
            color: #374151;
        }
        
        .fi-btn-color-gray:hover {
            border-color: #9ca3af;
            background-color: #f9fafb;
        }
        
        /* Checkbox styling */
        .fi-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }
        
        /* Link styling */
        a {
            color: #0d9488;
            text-decoration: none;
            font-weight: 500;
        }
        
        a:hover {
            color: #0f766e;
            text-decoration: underline;
        }
        
        /* Placeholder content styling */
        .fi-placeholder {
            text-align: center;
            margin: 1.5rem 0;
        }
        
        /* Section divider */
        .fi-section {
            position: relative;
            text-align: center;
            margin: 2rem 0;
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
        }
        
        .fi-section .fi-placeholder {
            position: relative;
            background-color: white;
            padding: 0 1rem;
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: -0.5rem;
            display: inline-block;
        }
        
        /* Action groups */
        .fi-ac {
            margin: 1rem 0;
        }
        
        .fi-ac-action-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }
        
        /* Responsive design */
        @media (max-width: 640px) {
            .bg-white {
                margin: 1rem;
                border-radius: 0.5rem;
            }
        }
    </style>
</div>