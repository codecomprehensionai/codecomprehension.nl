<x-card>
    <div class='flex gap-3 items-center justify-start rounded-lg'>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-code">
            <polyline points="16,18 22,12 16,6" />
            <polyline points="8,6 2,12 8,18" />
        </svg>
        <span>Code to analyse</span>
    </div>
    <div data-slot="card-description" class="text-muted-foreground text-sm">
        Study this code carefully before answering the questions
    </div>
    <div class="line-numbers relative rounded-lg bg-slate-900 p-2">
        <div class="relative flex text-center">
            <div class="flex pl-3.5 pt-3"><svg viewBox="0 0 24 24" fill="currentColor"
                    class="-ml-0.5 mr-1.5 h-3 w-3 text-red-500/20">
                </svg></div><span class="absolute inset-x-0 top-2 text-xs text-slate-500">foo</span>
        </div>
        <pre class="line-number"><code class="language-{{ $question->language }}">import sys
from PyQt5.QtWidgets import QMainWindow, QApplication, QPushButton
from PyQt5.QtCore import pyqtSlot, QFile, QTextStream

from sidebar_ui import Ui_MainWindow
            </code></pre>
    </div>
</x-card>
