/**
 * Auth Illustration Component
 * Renders theme-aware SVG illustrations for authentication pages
 * Uses CSS variables for primary color to match the current theme
 */

import { cn } from "@/lib/utils";

/**
 * Pre-defined illustration names for auth pages
 */
export const AuthIllustrations = {
    LOGIN: 'login',
    REGISTER: 'register',
    FORGOT_PASSWORD: 'forgot-password',
    RESET_PASSWORD: 'forgot-password',
    VERIFY_EMAIL: 'verify-email',
};

/**
 * Login Illustration - Mobile device with login form
 */
function LoginIllustration({ className }) {
    return (
        <svg
            className={cn("w-full h-auto", className)}
            viewBox="0 0 803 617"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            {/* Background elements */}
            <polygon points="184.898 427 247.069 329.231 107.835 240.694 35.931 353.769 151.095 427 184.898 427" className="fill-muted-foreground/10" />
            <path d="M471.5,599h-154V568h154Zm-152-2h150V570h-150Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />

            {/* Decorative circles - using primary color */}
            <circle cx="254" cy="442" r="10" className="fill-primary" />
            <circle cx="226" cy="442" r="10" className="fill-primary" />
            <circle cx="198" cy="442" r="10" className="fill-primary" />
            <circle cx="170" cy="442" r="10" className="fill-primary" />
            <circle cx="142" cy="442" r="10" className="fill-primary" />

            {/* Phone frame background */}
            <path d="M654.5,254.5v-76h38v-37h-186v186h159V309.31439A25.81441,25.81441,0,0,1,691.31445,283.5H692.5v-29Z" transform="translate(-198.5 -141.5)" className="fill-muted-foreground/10" />
            <rect x="538" y="480" width="124" height="26" className="fill-primary" />
            <rect x="507" y="352" width="182" height="24" className="fill-muted-foreground/10" />
            <rect y="615" width="778" height="2" className="fill-foreground/80" />

            {/* Phone body */}
            <path d="M918.68555,758.5H691.31445A26.84532,26.84532,0,0,1,664.5,731.68555V309.31445A26.845,26.845,0,0,1,691.31445,282.5h227.3711A26.845,26.845,0,0,1,945.5,309.31445v422.3711A26.84532,26.84532,0,0,1,918.68555,758.5Zm-227.3711-474A24.84271,24.84271,0,0,0,666.5,309.31445v422.3711A24.84271,24.84271,0,0,0,691.31445,756.5h227.3711A24.84271,24.84271,0,0,0,943.5,731.68555V309.31445A24.84271,24.84271,0,0,0,918.68555,284.5Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <path d="M778,302.5a6,6,0,1,1,6-6A6.00657,6.00657,0,0,1,778,302.5Zm0-10a4,4,0,1,0,4,4A4.00427,4.00427,0,0,0,778,292.5Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <rect x="594.5" y="154" width="44" height="2" className="fill-foreground/80" />

            {/* Corner accent */}
            <path d="M927.5,311.5v80a80.00357,80.00357,0,0,1-80-80Z" transform="translate(-198.5 -141.5)" className="fill-primary" />

            {/* Phone screen */}
            <path d="M928.5,731.5h-247v-421h247Zm-245-2h243v-417h-243Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />

            {/* User avatar */}
            <circle cx="671" cy="218" r="27" className="fill-foreground/10" />
            <circle cx="667" cy="221" r="27" className="fill-primary" />

            {/* Form elements */}
            <rect x="494" y="186" width="38" height="14" className="fill-foreground/80" />
            <rect x="515" y="319" width="38" height="14" className="fill-foreground/80" />
            <path d="M897.5,512.5h-184v-26h184Zm-182-2h180v-22h-180Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <rect x="507" y="428" width="182" height="24" className="fill-muted-foreground/10" />
            <rect x="515" y="395" width="38" height="14" className="fill-foreground/80" />
            <path d="M897.5,588.5h-184v-26h184Zm-182-2h180v-22h-180Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <path d="M868.5,641.5h-126v-28h126Zm-124-2h122v-24h-122Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />

            {/* Radio buttons */}
            <path d="M892.5,502.5a10.00094,10.00094,0,0,1-17.71,6.37l-.01-.01a9.99931,9.99931,0,1,1,17.43-8.73.00979.00979,0,0,0,.01.01A10.22724,10.22724,0,0,1,892.5,502.5Z" transform="translate(-198.5 -141.5)" className="fill-primary" />
            <path d="M892.5,502.5a10.00094,10.00094,0,0,1-17.71,6.37l-.01-.01a10.22763,10.22763,0,0,1-.28-2.36,10.00094,10.00094,0,0,1,17.71-6.37.00979.00979,0,0,0,.01.01A10.22724,10.22724,0,0,1,892.5,502.5Z" transform="translate(-198.5 -141.5)" className="fill-foreground/10" />
            <circle cx="688" cy="367" r="10" className="fill-primary" />
            <path d="M892.5,578.5a10.00094,10.00094,0,0,1-17.71,6.37l-.01-.01a9.99931,9.99931,0,1,1,17.43-8.73.00979.00979,0,0,0,.01.01A10.22724,10.22724,0,0,1,892.5,578.5Z" transform="translate(-198.5 -141.5)" className="fill-primary" />
            <path d="M892.5,578.5a10.00094,10.00094,0,0,1-17.71,6.37l-.01-.01a10.22763,10.22763,0,0,1-.28-2.36,10.00094,10.00094,0,0,1,17.71-6.37.00979.00979,0,0,0,.01.01A10.22724,10.22724,0,0,1,892.5,578.5Z" transform="translate(-198.5 -141.5)" className="fill-foreground/10" />
            <circle cx="688" cy="443" r="10" className="fill-primary" />

            {/* Decorative squares */}
            <rect x="468" y="48" width="52" height="52" className="fill-muted-foreground/10" />
            <path d="M869.5,227.5a12,12,0,1,1,12-12A12.01343,12.01343,0,0,1,869.5,227.5Zm0-22a10,10,0,1,0,10,10A10.01146,10.01146,0,0,0,869.5,205.5Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <path d="M605.5,394.5h-26v-26h26Zm-24-2h22v-22h-22Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <path d="M1001.5,588.5h-26v-26h26Zm-24-2h22v-22h-22Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />

            {/* Person */}
            <path d="M488.50263,414.16645l4.4618,2.40312,15.18222,8.18251,24.51011,13.20583,14.2838-26.50149a21.52495,21.52495,0,0,0-8.74393-29.189,28.59037,28.59037,0,0,0-33.38144,4.5292l-.00749.00749a28.62268,28.62268,0,0,0-5.37511,7.07455Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <polygon points="356.9 542.937 353.906 575.876 342.676 577.373 335.19 539.942 356.9 542.937" className="fill-primary/60" />
            <path d="M538.18164,718.87346s14.22395-7.48629,17.21847-1.49726c0,0-2.24589,17.9671,6.73766,20.213s12.72669,17.96709,0,19.46435-20.96161-2.99451-25.45339-2.99451,0-15.72121,0-15.72121Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <polygon points="266.316 542.937 269.311 575.876 280.54 577.373 288.026 539.942 266.316 542.937" className="fill-primary/60" />
            <path d="M482.03446,718.87346s-14.224-7.48629-17.21846-1.49726c0,0,2.24588,17.9671-6.73767,20.213s-12.72669,17.96709,0,19.46435S479.04,754.059,483.53172,754.059s0-15.72121,0-15.72121Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <circle cx="327.70358" cy="259.20612" r="19.46436" className="fill-primary/60" />
            <path d="M464.06737,438.8862,479.78858,463.591l18.71572-1.49726,30.69379,2.24589s2.89719-5.79438,5.26289-11.43158a44.28506,44.28506,0,0,0,2.972-8.78141c.74863-5.2404-9.73218-10.4808-9.73218-10.4808s-.41178-.07489-1.10046-.23957c-.9133-.21709-2.32082-.57646-3.86295-1.10051-3.48114-1.1828-7.666-3.18912-8.51191-6.14621-1.49726-5.24041,9.73218-12.7267,9.73218-12.7267l-13.47532-12.72669s-11.97057,16.627-26.50889,20.41508a23.155,23.155,0,0,1-2.56044.524c-.04487.00749-.08224.015-.12721.02248C465.56462,423.91362,464.06737,438.8862,464.06737,438.8862Z" transform="translate(-198.5 -141.5)" className="fill-primary/60" />
            <path d="M472.30229,432.14854l-8.23492,6.73766s-6.73767,32.19105-4.49178,41.1746-.74863,17.21846-.74863,18.71572S452.0893,556.421,452.0893,556.421s-13.47532,29.94516.74863,26.95064,8.98355-32.93967,8.98355-32.93967l17.21847-52.404V453.85878Z" transform="translate(-198.5 -141.5)" className="fill-primary/60" />
            <path d="M531.444,438.13757l5.989,4.49177,2.99452,62.88484,22.45887,53.90129s16.46984,26.202,5.989,26.95065S551.657,559.41547,551.657,559.41547l-20.213-51.6554-2.99452-50.15814Z" transform="translate(-198.5 -141.5)" className="fill-primary/60" />
            <path d="M494.01253,442.62934s14.97258,10.48081,38.92871,6.73767c0,0,6.73766,13.47532,2.99451,20.96161s4.49178,25.45339,5.989,27.69927,18.71572,48.66089,24.70475,98.819,22.45888,84.59508,8.23492,89.08685-43.42048,9.73218-46.415,5.24041-7.48629-116.03751-7.48629-116.03751l-8.23492,42.67186s11.22944,72.617,2.99452,74.11427-59.89032-2.99451-58.39307-11.97806,16.46984-56.14718,16.46984-56.14718-.74863-94.32726,14.224-112.29435c0,0,5.989-9.73218-2.24589-22.45888s-7.48629-32.93967-7.48629-32.93967S484.28035,456.10467,494.01253,442.62934Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <polygon points="282.913 280.145 295.513 307.867 298.507 306.37 285.473 279.621 282.913 280.145" className="fill-foreground/80" />
            <path d="M522.73742,432.30572l9.45519,20.80443,2.26837-.20214-.77111-3.541-7.0895-15.96078C525.68707,433.18914,524.27955,432.82977,522.73742,432.30572Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <path d="M492.96443,416.56957a12.323,12.323,0,0,0,22.60865,2.201c-9.47017-12.38235-1.34757-20.16057,14.0368-26.04483a12.31224,12.31224,0,0,0-1.32509-13.655,28.687,28.687,0,0,0-23.4696,7.72586l-.00749.00749L493.87783,407.077A12.26371,12.26371,0,0,0,492.96443,416.56957Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <ellipse cx="316.09983" cy="261.45201" rx="2.6202" ry="4.49177" className="fill-primary/60" />
            <path d="M522.408,395.6229l22.50383,10.19632,4.125-9.10331a21.56313,21.56313,0,0,0-4.34208-9.08087l-15.48909-7.01465Z" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
            <circle cx="378" cy="445" r="10" className="fill-primary" />

            {/* Plant */}
            <circle cx="56.95651" cy="507.91124" r="6.53537" className="fill-primary/70" />
            <rect x="87.9751" y="541.97266" width="2" height="74.41113" className="fill-foreground/80" />
            <circle cx="88.97529" cy="541.97239" r="10.52282" className="fill-foreground/80" />
            <path d="M287.47529,729.33366s-1.50326-32.33193-32.32009-28.57378" transform="translate(-198.5 -141.5)" className="fill-foreground/80" />
        </svg>
    );
}

/**
 * Register Illustration - Person with security elements
 */
function RegisterIllustration({ className }) {
    return (
        <svg
            className={cn("w-full h-auto", className)}
            viewBox="0 0 888 742"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            {/* Background shape */}
            <ellipse cx="444" cy="680" rx="444" ry="61.5" className="fill-muted-foreground/5" />

            {/* Main device/form */}
            <rect x="234" y="100" width="420" height="520" rx="20" className="fill-background stroke-foreground/20" strokeWidth="2" />

            {/* Header area */}
            <rect x="254" y="120" width="380" height="60" rx="8" className="fill-muted" />
            <circle cx="290" cy="150" r="20" className="fill-primary" />
            <rect x="324" y="140" width="120" height="8" rx="4" className="fill-foreground/30" />
            <rect x="324" y="156" width="80" height="6" rx="3" className="fill-foreground/20" />

            {/* Form fields */}
            <rect x="254" y="200" width="380" height="50" rx="8" className="fill-muted stroke-foreground/10" strokeWidth="1" />
            <rect x="274" y="218" width="100" height="14" rx="2" className="fill-foreground/20" />

            <rect x="254" y="270" width="380" height="50" rx="8" className="fill-muted stroke-foreground/10" strokeWidth="1" />
            <rect x="274" y="288" width="140" height="14" rx="2" className="fill-foreground/20" />

            <rect x="254" y="340" width="380" height="50" rx="8" className="fill-muted stroke-foreground/10" strokeWidth="1" />
            <circle cx="608" cy="365" r="12" className="fill-primary/30" />
            <rect x="274" y="358" width="80" height="14" rx="2" className="fill-foreground/20" />

            <rect x="254" y="410" width="380" height="50" rx="8" className="fill-muted stroke-foreground/10" strokeWidth="1" />
            <circle cx="608" cy="435" r="12" className="fill-primary/30" />
            <rect x="274" y="428" width="100" height="14" rx="2" className="fill-foreground/20" />

            {/* Submit button */}
            <rect x="254" y="490" width="380" height="50" rx="8" className="fill-primary" />
            <rect x="374" y="508" width="140" height="14" rx="2" className="fill-primary-foreground/80" />

            {/* Checkboxes */}
            <rect x="254" y="560" width="16" height="16" rx="3" className="fill-primary" />
            <rect x="280" y="562" width="200" height="12" rx="2" className="fill-foreground/20" />

            {/* Decorative elements */}
            <circle cx="120" cy="200" r="40" className="fill-primary/20" />
            <circle cx="780" cy="350" r="30" className="fill-primary/15" />
            <circle cx="100" cy="500" r="25" className="fill-primary/10" />

            {/* Person illustration */}
            <g transform="translate(650, 450)">
                {/* Body */}
                <ellipse cx="60" cy="200" rx="45" ry="15" className="fill-foreground/10" />
                <path d="M30 180 Q60 200 90 180 L85 120 Q60 130 35 120 Z" className="fill-primary" />
                {/* Head */}
                <circle cx="60" cy="90" r="35" className="fill-primary/60" />
                {/* Arms */}
                <path d="M25 130 Q0 150 10 180" className="stroke-primary" strokeWidth="12" strokeLinecap="round" fill="none" />
                <path d="M95 130 Q120 150 110 180" className="stroke-primary" strokeWidth="12" strokeLinecap="round" fill="none" />
            </g>

            {/* Security badge */}
            <g transform="translate(70, 300)">
                <circle cx="40" cy="40" r="40" className="fill-primary/20" />
                <path d="M40 15 L55 25 L55 45 Q55 60 40 70 Q25 60 25 45 L25 25 Z" className="fill-primary" />
                <path d="M35 45 L40 50 L50 35" className="stroke-primary-foreground" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" fill="none" />
            </g>

            {/* Floating elements */}
            <rect x="750" y="150" width="60" height="60" rx="10" className="fill-primary/10 stroke-primary/30" strokeWidth="1" />
            <circle cx="780" cy="180" r="15" className="fill-primary/40" />

            <rect x="50" y="600" width="80" height="80" rx="12" className="fill-muted stroke-foreground/10" strokeWidth="1" />
            <rect x="70" y="620" width="40" height="6" rx="3" className="fill-foreground/20" />
            <rect x="70" y="635" width="30" height="6" rx="3" className="fill-foreground/15" />
            <rect x="70" y="650" width="35" height="6" rx="3" className="fill-primary/40" />
        </svg>
    );
}

/**
 * Forgot Password Illustration - Key and lock theme
 */
function ForgotPasswordIllustration({ className }) {
    return (
        <svg
            className={cn("w-full h-auto", className)}
            viewBox="0 0 800 600"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            {/* Background elements */}
            <ellipse cx="400" cy="550" rx="350" ry="50" className="fill-muted-foreground/5" />

            {/* Main lock body */}
            <g transform="translate(250, 150)">
                {/* Lock shackle */}
                <path
                    d="M100 120 L100 70 Q100 20 150 20 Q200 20 200 70 L200 120"
                    className="stroke-foreground/60"
                    strokeWidth="25"
                    strokeLinecap="round"
                    fill="none"
                />

                {/* Lock body */}
                <rect x="50" y="120" width="200" height="160" rx="20" className="fill-primary" />

                {/* Keyhole */}
                <circle cx="150" cy="180" r="25" className="fill-primary-foreground/30" />
                <rect x="140" y="180" width="20" height="50" rx="5" className="fill-primary-foreground/30" />

                {/* Lock shine */}
                <rect x="70" y="140" width="8" height="40" rx="4" className="fill-primary-foreground/20" />
            </g>

            {/* Key floating */}
            <g transform="translate(480, 280) rotate(30)">
                {/* Key head */}
                <circle cx="30" cy="30" r="30" className="fill-primary/80 stroke-primary" strokeWidth="3" />
                <circle cx="30" cy="30" r="15" className="fill-background" />

                {/* Key shaft */}
                <rect x="55" y="22" width="100" height="16" rx="3" className="fill-primary/80" />

                {/* Key teeth */}
                <rect x="120" y="38" width="15" height="15" rx="2" className="fill-primary/80" />
                <rect x="140" y="38" width="10" height="20" rx="2" className="fill-primary/80" />
            </g>

            {/* Email envelope */}
            <g transform="translate(100, 350)">
                <rect x="0" y="20" width="120" height="80" rx="8" className="fill-muted stroke-foreground/20" strokeWidth="2" />
                <path d="M0 28 L60 70 L120 28" className="stroke-primary" strokeWidth="3" fill="none" />
                <circle cx="100" cy="90" r="15" className="fill-primary" />
                <path d="M95 90 L100 95 L108 85" className="stroke-primary-foreground" strokeWidth="2" strokeLinecap="round" fill="none" />
            </g>

            {/* Question marks floating */}
            <text x="550" y="150" className="fill-primary/40" style={{ fontSize: "60px", fontFamily: "sans-serif", fontWeight: "bold" }}>?</text>
            <text x="620" y="200" className="fill-primary/20" style={{ fontSize: "40px", fontFamily: "sans-serif", fontWeight: "bold" }}>?</text>
            <text x="150" y="180" className="fill-primary/30" style={{ fontSize: "50px", fontFamily: "sans-serif", fontWeight: "bold" }}>?</text>

            {/* Decorative circles */}
            <circle cx="680" cy="300" r="40" className="fill-primary/10" />
            <circle cx="120" cy="250" r="30" className="fill-primary/15" />
            <circle cx="700" cy="450" r="25" className="fill-primary/20" />

            {/* Person thinking */}
            <g transform="translate(550, 380)">
                {/* Shadow */}
                <ellipse cx="50" cy="140" rx="35" ry="10" className="fill-foreground/10" />
                {/* Body */}
                <path d="M25 130 Q50 145 75 130 L70 80 Q50 90 30 80 Z" className="fill-primary/70" />
                {/* Head */}
                <circle cx="50" cy="55" r="30" className="fill-primary/50" />
                {/* Thinking hand */}
                <ellipse cx="80" cy="50" rx="8" ry="6" className="fill-primary/50" />
            </g>

            {/* Dots pattern */}
            <circle cx="50" cy="100" r="5" className="fill-primary/30" />
            <circle cx="70" cy="120" r="4" className="fill-primary/20" />
            <circle cx="45" cy="140" r="3" className="fill-primary/25" />

            <circle cx="750" cy="150" r="5" className="fill-primary/30" />
            <circle cx="730" cy="170" r="4" className="fill-primary/20" />
            <circle cx="755" cy="190" r="3" className="fill-primary/25" />
        </svg>
    );
}

/**
 * Verify Email Illustration
 */
function VerifyEmailIllustration({ className }) {
    return (
        <svg
            className={cn("w-full h-auto", className)}
            viewBox="0 0 800 600"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            {/* Background */}
            <ellipse cx="400" cy="550" rx="350" ry="50" className="fill-muted-foreground/5" />

            {/* Main envelope */}
            <g transform="translate(200, 150)">
                {/* Envelope back */}
                <rect x="0" y="50" width="400" height="250" rx="15" className="fill-muted stroke-foreground/20" strokeWidth="2" />

                {/* Envelope flap */}
                <path d="M0 65 L200 180 L400 65" className="fill-primary/20 stroke-foreground/20" strokeWidth="2" />

                {/* Letter coming out */}
                <rect x="50" y="20" width="300" height="200" rx="10" className="fill-background stroke-foreground/10" strokeWidth="2" />
                <rect x="80" y="60" width="180" height="12" rx="3" className="fill-foreground/20" />
                <rect x="80" y="85" width="240" height="10" rx="3" className="fill-foreground/15" />
                <rect x="80" y="105" width="200" height="10" rx="3" className="fill-foreground/15" />
                <rect x="80" y="125" width="220" height="10" rx="3" className="fill-foreground/15" />

                {/* Checkmark on letter */}
                <circle cx="200" cy="170" r="30" className="fill-primary" />
                <path d="M185 170 L195 180 L220 155" className="stroke-primary-foreground" strokeWidth="5" strokeLinecap="round" strokeLinejoin="round" fill="none" />
            </g>

            {/* Flying paper planes */}
            <g transform="translate(600, 100) rotate(15)">
                <path d="M0 20 L60 0 L60 15 L20 20 L60 25 L60 40 Z" className="fill-primary/60" />
            </g>
            <g transform="translate(100, 80) rotate(-10)">
                <path d="M0 15 L45 0 L45 12 L15 15 L45 18 L45 30 Z" className="fill-primary/40" />
            </g>

            {/* Notification bells */}
            <g transform="translate(650, 300)">
                <circle cx="30" cy="30" r="35" className="fill-primary/20" />
                <path d="M30 10 L30 5" className="stroke-primary" strokeWidth="3" strokeLinecap="round" />
                <path d="M15 45 Q30 55 45 45 L42 25 Q42 12 30 12 Q18 12 18 25 Z" className="fill-primary" />
                <circle cx="30" cy="50" r="6" className="fill-primary" />
            </g>

            {/* Email @ symbol */}
            <text x="80" y="450" className="fill-primary/30" style={{ fontSize: "80px", fontFamily: "sans-serif", fontWeight: "bold" }}>@</text>

            {/* Decorative elements */}
            <circle cx="700" cy="450" r="30" className="fill-primary/15" />
            <circle cx="150" cy="350" r="20" className="fill-primary/10" />

            {/* Stars/sparkles */}
            <g transform="translate(500, 120)">
                <path d="M10 0 L12 8 L20 10 L12 12 L10 20 L8 12 L0 10 L8 8 Z" className="fill-primary/60" />
            </g>
            <g transform="translate(680, 200)">
                <path d="M8 0 L10 6 L16 8 L10 10 L8 16 L6 10 L0 8 L6 6 Z" className="fill-primary/40" />
            </g>
            <g transform="translate(120, 200)">
                <path d="M6 0 L7 5 L12 6 L7 7 L6 12 L5 7 L0 6 L5 5 Z" className="fill-primary/50" />
            </g>
        </svg>
    );
}

/**
 * AuthIllustration - Theme-aware illustration component
 * Uses CSS variables for colors to match the current theme
 */
export function AuthIllustration({
    name,
    className = "",
    alt = "Illustration",
}) {
    const illustrations = {
        'login': LoginIllustration,
        'register': RegisterIllustration,
        'forgot-password': ForgotPasswordIllustration,
        'verify-email': VerifyEmailIllustration,
    };

    const IllustrationComponent = illustrations[name];

    if (!IllustrationComponent) {
        // Fallback to static image if illustration not found
        return (
            <img
                src={`/images/illustrations/${name}.svg`}
                alt={alt}
                className={cn("w-full max-w-md", className)}
                loading="lazy"
            />
        );
    }

    return <IllustrationComponent className={cn("max-w-md", className)} />;
}

export default AuthIllustration;
