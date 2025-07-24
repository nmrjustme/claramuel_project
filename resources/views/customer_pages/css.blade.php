<style>
    .hover-effect {
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .hover-effect:hover {
        transform: scale(1.05);
        filter: brightness(0.85);
    }

    #confirmationModal {
        transition: opacity 0.3s ease;
    }

    #selectedCottagesList::-webkit-scrollbar {
        width: 5px;
    }

    #selectedCottagesList::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 10px;
    }
</style>