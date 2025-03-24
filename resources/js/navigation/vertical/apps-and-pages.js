// TODO: Get type from backend
const userData = useCookie('userData')
const valor = localStorage.getItem("menu");
const menu = JSON.parse(valor);
const nuevoMenu = transformMenuData(menu);

function transformMenuData(modules) {
    return modules
    .filter(module => module.padre !== 1 || (module.submodulos && module.submodulos.length > 0))
    .map(module => {
        let transformedModule = {
            title: module.nombre,
            icon: module.icono ? { icon: module.icono } : undefined,
        };

        if (module.ruta) {
            transformedModule.to = module.ruta;
        }

        if (module.submodulos && module.submodulos.length > 0) {
            transformedModule.children = module.submodulos.map(sub => transformSubmodule(sub)).filter(sub => sub !== null); // Filtra los submódulos vacíos;
        }

        return transformedModule;
    });
}

function transformSubmodule(submodule) {
    // Si el submódulo tiene padre = 1 y no tiene submódulos, lo ignoramos
    if (submodule.padre === 1 && (!submodule.submodulos || submodule.submodulos.length === 0)) {
        return null;
    }

    let transformedSubmodule = {
        title: submodule.nombre,
    };

    if (submodule.ruta) {
        transformedSubmodule.to = submodule.ruta;
    }

    if (submodule.submodulos && submodule.submodulos.length > 0) {
        transformedSubmodule.children = submodule.submodulos.map(sub => transformSubmodule(sub));
    }

    return transformedSubmodule;
}

export default nuevoMenu;

