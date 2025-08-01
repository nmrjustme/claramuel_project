module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./node_modules/flowbite/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#1a365d",
                redprimary: "#DC2626",
                secondary: "#e53e3e",
                accent: "#990F02",
                darkAccent: "#950606",
                lightGray: "#D3D3D3",
                darkGray: "#A0A0A0",
            },
            fontFamily: {
                sans: [
                    "Inter",
                    "system-ui",
                    "-apple-system",
                    "BlinkMacSystemFont",
                    "Segoe UI",
                    "Roboto",
                    "sans-serif",
                ],
            },
        },
    },
    plugins: [require("flowbite/plugin")],
};
