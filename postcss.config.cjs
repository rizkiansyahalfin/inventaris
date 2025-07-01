// export default {
//     plugins: {
//         tailwindcss: {},
//         autoprefixer: {},
//     },
// };

// module.exports = {
//     plugins: [
//       require('tailwindcss'),
//       require('autoprefixer'),
//     ]
//   }
  
module.exports = {
  plugins: [
    require('@tailwindcss/postcss')(),
    require('autoprefixer'),
  ]
}
