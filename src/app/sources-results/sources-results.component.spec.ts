import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SourcesResultsComponent } from './sources-results.component';

describe('SourcesResultsComponent', () => {
  let component: SourcesResultsComponent;
  let fixture: ComponentFixture<SourcesResultsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SourcesResultsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SourcesResultsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
