/*Tab Switch*/

function switchTab(tabName,button){

    // Make sure all tab button and panel deactivate
    document.querySelectorAll('.panel').forEach(function(panel){
        panel.classList.remove('active');
    });
    document.querySelectorAll('.tab').forEach(function(tab){
        tab.classList.remove('active');
    });

    // Selected tab and panel
    document.getElementById('panel-' + tabName).classList.add('active');
    button.classList.add('active');

    //Show tab navigation
    document.getElementById('breadcrumb').style.display ='none';
    document.getElementById('tabs').style.display = 'flex';
}

// -------------- VIEW DETAIL -----------------
function viewGuideline(data){

    updateBreadcrumb('Guideline',data.Title);

    var Information = '';
    if(data.Budget || data.PetType || data.Organization){
        Information = `
        <div class= "meta-grid" style="margin-top: 16px;">
            <div class="meta-item">
                <div class="meta-label">Organization</div>
                <div class="meta-value">${escapeHTML(data.OrganizationName) || 'Unknown'}</div>
            </div>

             <div class="meta-item">
                <div class="meta-label">Pet Type</div>
                <div class="meta-value">${escapeHTML(data.PetType)}</div>
            </div>

             <div class="meta-item">
                <div class="meta-label">Budget</div>
                <div class="meta-value">${data.Budget? 'RM ' + parseFloat(data.Budget).toFixed(2) : 'N/A'}
                </div>
            </div>
        </div>`;
    }

    document.getElementById('view-content').innerHTML=`
        <div class="view-header">
            <div class="view-header-left">
                <div class= "icon-box">${escapeHTML(data.PetType)}</div>
                <div>
                    <div class = "view-title">${escapeHTML(data.Title)}</div>
                    <div class="view-meta">
                        ${escapeHTML(data.PetType)} &nbsp;.&nbsp; ${escapeHTML(data.OrganizationName)|| 'Unknown'}
                    </div>
                </div>
            </div>
            <button class = "btn-back" onclick="goBack()"> Back </button>
        </div>
        <div class="view-body">
            <div class = "section-label"> Details </div>
            <div class ="body-text">${escapeHTML(data.Description)}</div>
        </div>
    `;

    showDetailPanel();
}

function viewFaq(data){

    updateBreadcrumb('FAQ',data.Question);

    document.getElementById('view-content').innerHTML = `
        <div class= "view-header">
        <div class="view-header-left">
            <div>
                <div class = "view-title">${escapeHTML(data.Question)}</div>
                <div class="view-meta">
                    By: ${escapeHTML(data.OrganizationName) || 'Unknown'}
                </div>
            </div>
        </div>
         <button class = "btn-back" onclick="goBack()"> Back </button>
        </div>

        <div class = "view-body">
            <div class = "section-label"> Answer </div>
            <div class="body-text">${escapeHTML(data.Description)}</div>
        </div>
    `;
    showDetailPanel();
}


function goBack(){
    document.getElementById('breadcrumb').style.display = 'none';
    document.getElementById('panel-view').classList.remove('active');

    document.getElementById('tabs').style.display = 'flex';

    var activeTab =  document.querySelector('.tab.active');
    var tabName = activeTab ? activeTav.textContent.trim().toLowerCase() : 'guideline';
    document.getElementById('panel-' + tabName).classList.add('active');
}

function updateBreadcrumb(tabName,itemName){
    document.getElementById('breadcrumb').style.display = 'flex';
    document.getElementById('breadcrumb-tab').textContent =  tabName;
    document.getElementById('breadcrumb-title').textContent =  itemName;
}

function showDetailPanel(){
     document.getElementById('tabs').style.display = 'none';
     document.querySelectorAll ('.panel').forEach(function(panel){
        panel.classList.remove('active');
     });
     document.getElementById('panel-view').classList.add('active');
}

function escapeHTML(text){
     if (!text) return '';
    return String(text)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#039;');
}