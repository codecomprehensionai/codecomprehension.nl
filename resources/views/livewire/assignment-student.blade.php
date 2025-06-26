@php
    $question = $assignment->questions[$index] ?? null;
@endphp
<div>
    <x-header />
    <div class="max-w-7xl mx-auto px-6 py-8">
        <x-assignment.title :$assignment />
        <x-assignment.progress :$assignment :$index />
        <x-assignment.code :$question />
        <x-assignment.answer-box :$question :$index :assignment="$assignment" wire:previousQuestion="previousQuestion"
        wire:nextQuestion="nextQuestion" />
    </div>
            <pre class="line-number"><code class="language-{{ $question->language }}">import sys
from PyQt5.QtWidgets import QMainWindow, QApplication, QPushButton
from PyQt5.QtCore import pyqtSlot, QFile, QTextStream

from sidebar_ui import Ui_MainWindow
            </code></pre>
</div>
