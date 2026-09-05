import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { manualContent } from '@/data/manual-content';
import { useAuthStore } from './auth';

export const useManualStore = defineStore('manual', () => {
    const searchQuery = ref('');
    const currentSection = ref('introduccion');
    const expandedSections = ref([]);

    const userRoles = computed(() => {
        const authStore = useAuthStore();
        return authStore.user?.roles || [];
    });

    const filteredContent = computed(() => {
        const result = {
            general: { ...manualContent.general },
            academic: { ...manualContent.academic },
            admin: { ...manualContent.admin },
            troubleshooting: { ...manualContent.troubleshooting }
        };

        Object.keys(result).forEach(key => {
            if (result[key].sections) {
                result[key].sections = result[key].sections.filter(section => {
                    return section.roles.includes('all') || 
                           section.roles.some(role => userRoles.value.includes(role));
                });
            }
        });

        return result;
    });

    const allSections = computed(() => {
        const sections = [];
        Object.keys(filteredContent.value).forEach(category => {
            const categoryData = filteredContent.value[category];
            if (categoryData.sections) {
                categoryData.sections.forEach(section => {
                    sections.push({
                        ...section,
                        category: categoryData.title || category
                    });
                });
            }
        });
        return sections;
    });

    const currentSectionData = computed(() => {
        return allSections.value.find(s => s.id === currentSection.value);
    });

    const searchResults = computed(() => {
        if (!searchQuery.value.trim()) return [];
        const query = searchQuery.value.toLowerCase();
        return allSections.value.filter(section => {
            return section.title.toLowerCase().includes(query) ||
                   section.content.toLowerCase().includes(query);
        });
    });

    const totalSections = computed(() => allSections.value.length);

    const isSectionExpanded = (sectionId) => {
        return expandedSections.value.includes(sectionId);
    };

    function setCurrentSection(sectionId) {
        currentSection.value = sectionId;
        if (!expandedSections.value.includes(sectionId)) {
            expandedSections.value.push(sectionId);
        }
    }

    function toggleSection(sectionId) {
        const index = expandedSections.value.indexOf(sectionId);
        if (index > -1) {
            expandedSections.value.splice(index, 1);
        } else {
            expandedSections.value.push(sectionId);
        }
    }

    function resetSearch() {
        searchQuery.value = '';
    }

    function goToFirstSection() {
        if (allSections.value.length > 0) {
            setCurrentSection(allSections.value[0].id);
        }
    }

    function nextSection() {
        const currentIndex = allSections.value.findIndex(s => s.id === currentSection.value);
        if (currentIndex < allSections.value.length - 1) {
            setCurrentSection(allSections.value[currentIndex + 1].id);
        }
    }

    function previousSection() {
        const currentIndex = allSections.value.findIndex(s => s.id === currentSection.value);
        if (currentIndex > 0) {
            setCurrentSection(allSections.value[currentIndex - 1].id);
        }
    }

    const navigationInfo = computed(() => {
        const currentIndex = allSections.value.findIndex(s => s.id === currentSection.value);
        return {
            currentIndex: currentIndex + 1,
            total: allSections.value.length,
            hasNext: currentIndex < allSections.value.length - 1,
            hasPrevious: currentIndex > 0
        };
    });

    return {
        searchQuery,
        currentSection,
        expandedSections,
        userRoles,
        filteredContent,
        allSections,
        currentSectionData,
        searchResults,
        totalSections,
        navigationInfo,
        isSectionExpanded,
        setCurrentSection,
        toggleSection,
        resetSearch,
        goToFirstSection,
        nextSection,
        previousSection
    };
});