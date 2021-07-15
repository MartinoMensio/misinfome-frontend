import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './home/home.component';
import { SearchComponent } from './search/search.component';
import { ProfilesResultsComponent } from './profiles-results/profiles-results.component';
import { HttpClientModule } from '@angular/common/http';
import { CountUpModule } from 'ngx-countup';
import { NgxPaginationModule } from 'ngx-pagination';
import { AboutComponent } from './about/about.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ContactModalComponent } from './contact-modal/contact-modal.component';
import { AnalysisModalComponent } from './analysis-modal/analysis-modal.component';
import { AccuracyModalComponent } from './accuracy-modal/accuracy-modal.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    SearchComponent,
    ProfilesResultsComponent,
    AboutComponent,
    ContactModalComponent,
    AnalysisModalComponent,
    AccuracyModalComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    CountUpModule,
    NgxPaginationModule,
    NgbModule,
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
