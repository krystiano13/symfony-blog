const aboutSection = document.querySelector('#about');
const hamburgerButton = document.querySelector('#hamburger');
const backButton = document.querySelector('#back');

const aboutClass = "transition flex gap-5 flex-col justify-start items-center p-4 w-72 bg-gray-50 h-[100vh] xl:translate-x-0 fixed z-50";

let hidden = true;

function detectChanges() {
    if(hidden) {
        aboutSection.className = `${aboutClass} translate-x-[-100vw]`;
    }
    else {
        aboutSection.className = `${aboutClass}`;
    }
}

backButton.addEventListener('click', () => {
    hidden = true;
    detectChanges();
})

hamburgerButton.addEventListener('click', () => {
    hidden = !hidden;
    detectChanges();
});
