<footer class="footer" id="footer">
<div id="dynamic-footer-container"> </div>
</footer>

<script>
(function() {
    function decodeFromCharCodes(codes) {
        let decodedText = '';
        for (let i = 0; i < codes.length; i++) {
            decodedText += String.fromCharCode(codes[i]);
        }
        return decodedText;
    }

    const aTagCodes = [97];
    const iTagCodes = [105];

    const navBrandClassCodes = [110, 97, 118, 45, 98, 114, 97, 110, 100];
    const faSolidClassCodes = [102, 97, 45, 115, 111, 108, 105, 100];
    const faPawClassCodes = [102, 97, 45, 112, 97, 119];

    const indexHrefCodes = [46, 46, 47, 105, 110, 100, 101, 120, 46, 112, 104, 112];
    const anubillTextCodes = [65, 78, 85, 66, 73, 76, 76];

    const container = document.getElementById('dynamic-nav-brand-container');

    if (container) {
        const navBrandLink = document.createElement(decodeFromCharCodes(aTagCodes));
        navBrandLink.href = decodeFromCharCodes(indexHrefCodes);
        navBrandLink.className = decodeFromCharCodes(navBrandClassCodes);

        const iconElement = document.createElement(decodeFromCharCodes(iTagCodes));
        iconElement.classList.add(decodeFromCharCodes(faSolidClassCodes));
        iconElement.classList.add(decodeFromCharCodes(faPawClassCodes));

        navBrandLink.appendChild(iconElement);

        navBrandLink.innerHTML += '&nbsp;'; 

        navBrandLink.appendChild(document.createTextNode(decodeFromCharCodes(anubillTextCodes)));

        container.appendChild(navBrandLink);
    }
})();
</script>

<script>
(function() {
    function decodeFromCharCodes(codes) {
        let decodedText = '';
        for (let i = 0; i < codes.length; i++) {
            decodedText += String.fromCharCode(codes[i]);
        }
        return decodedText;
    }

    const divTagCodes = [100, 105, 118];
    const spanTagCodes = [115, 112, 97, 110];
    const aTagCodes = [97];

    const footerContentClassCodes = [102, 111, 111, 116, 101, 114, 45, 99, 111, 110, 116, 101, 110, 116];
    const footerLinksClassCodes = [102, 111, 111, 116, 101, 114, 45, 108, 105, 110, 107, 115];

    const companyTextCodes = [
        169, 32,
        80, 84, 46, 32, 65, 114, 101, 97, 32, 78, 101, 97, 114, 32,
        85, 114, 98, 97, 110, 32, 78, 101, 116, 115, 105, 110, 100, 111,
        46, 32, 65, 108, 108, 32, 114, 105, 103, 104, 116, 115, 32,
        114, 101, 115, 101, 114, 118, 101, 100, 46
    ];

    const projectUrlCodes = [
        104, 116, 116, 112, 58, 47, 47, 100, 111, 110, 105, 101, 116, 104,
        97, 109, 98, 97, 115, 46, 109, 121, 46, 105, 100, 47
    ];
    const projectTextCodes = [80, 114, 111, 106, 101, 99, 116];

    const supportUrlCodes = [
        104, 116, 116, 112, 58, 47, 47, 97, 110, 117, 110, 101, 116, 46,
        119, 101, 98, 46, 105, 100, 47
    ];
    const supportTextCodes = [83, 117, 112, 112, 111, 114, 116];

    const container = document.getElementById('dynamic-footer-container');

    if (container) {
        const footerContentDiv = document.createElement(decodeFromCharCodes(divTagCodes));
        footerContentDiv.className = decodeFromCharCodes(footerContentClassCodes);

        const copyrightWrapperDiv = document.createElement(decodeFromCharCodes(divTagCodes));

        const copyrightSpan = document.createElement(decodeFromCharCodes(spanTagCodes));
        
        const yearSpan = document.createElement(decodeFromCharCodes(spanTagCodes));
        yearSpan.textContent = new Date().getFullYear();
        copyrightSpan.appendChild(yearSpan);

        const companyTextSpan = document.createElement(decodeFromCharCodes(spanTagCodes));
        companyTextSpan.textContent = ' ' + decodeFromCharCodes(companyTextCodes);
        copyrightSpan.appendChild(companyTextSpan);
        
        copyrightWrapperDiv.appendChild(copyrightSpan);
        footerContentDiv.appendChild(copyrightWrapperDiv);

        const footerLinksDiv = document.createElement(decodeFromCharCodes(divTagCodes));
        footerLinksDiv.className = decodeFromCharCodes(footerLinksClassCodes);

        const projectLink = document.createElement(decodeFromCharCodes(aTagCodes));
        projectLink.href = decodeFromCharCodes(projectUrlCodes);
        projectLink.textContent = decodeFromCharCodes(projectTextCodes);
        footerLinksDiv.appendChild(projectLink);

        const supportLink = document.createElement(decodeFromCharCodes(aTagCodes));
        supportLink.href = decodeFromCharCodes(supportUrlCodes);
        supportLink.textContent = decodeFromCharCodes(supportTextCodes);
        footerLinksDiv.appendChild(supportLink);

        footerContentDiv.appendChild(footerLinksDiv);

        container.appendChild(footerContentDiv);
    }
})();
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('navToggle').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const footer = document.getElementById('footer');
        
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        footer.classList.toggle('expanded');
    });

    document.querySelectorAll('.sidebar-menu a').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const isMenuToggle = this.classList.contains('menu-toggle');
            const parentLi = this.closest('li');
            const hasSubmenu = parentLi.classList.contains('has-submenu');
            
            if (hasSubmenu && isMenuToggle) {
                e.preventDefault();
                
                const submenu = parentLi.querySelector('.submenu');
                const arrow = this.querySelector('.menu-arrow');
                
                submenu.classList.toggle('show');
                
                if (arrow) {
                    arrow.classList.toggle('rotated');
                }
                
                const siblings = Array.from(parentLi.parentNode.children).filter(child => child !== parentLi);
                siblings.forEach(function(sibling) {
                    const otherSubmenu = sibling.querySelector('.submenu');
                    const otherArrow = sibling.querySelector('.menu-arrow');
                    if (otherSubmenu) {
                        otherSubmenu.classList.remove('show');
                    }
                    if (otherArrow) {
                        otherArrow.classList.remove('rotated');
                    }
                });
            } else {
                document.querySelectorAll('.sidebar-menu a').forEach(function(item) {
                    item.classList.remove('active');
                });
                
                this.classList.add('active');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    function checkScreenSize() {
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.add('collapsed');
            document.getElementById('mainContent').classList.add('expanded');
            document.getElementById('footer').classList.add('expanded');
        }
    }

    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
</script>
</body>
</html>